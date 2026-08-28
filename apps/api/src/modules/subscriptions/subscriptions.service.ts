import { Injectable, NotFoundException, ConflictException } from '@nestjs/common';
import { PrismaService } from '../../common/prisma/prisma.service';
import { UsersService } from '../users/users.service';
import { BooksService } from '../books/books.service';

@Injectable()
export class SubscriptionsService {
  constructor(
    private prisma: PrismaService,
    private usersService: UsersService,
    private booksService: BooksService,
  ) {}

  async createSubscription(userId: number, bookId: number, idiomaIds: number[]) {
    // Check if user can add more subscriptions
    const limits = await this.usersService.getSubscriptionLimits(userId);
    if (!limits.can_add_more) {
      throw new ConflictException('Has reached your plan limit for books');
    }

    // Check if book exists and is approved
    const book = await this.prisma.libro.findUnique({
      where: { id: bookId },
      include: { enseñanzas: true },
    });

    if (!book) {
      throw new NotFoundException('Book not found');
    }

    if (book.estado !== 'aprobado') {
      throw new ConflictException('Book is not yet approved for subscriptions');
    }

    // Check if already subscribed
    const existingSubscription = await this.prisma.suscripcion.findUnique({
      where: {
        usuario_id_libro_id: {
          usuario_id: userId,
          libro_id: bookId,
        },
      },
    });

    if (existingSubscription) {
      throw new ConflictException('Already subscribed to this book');
    }

    // Check language limits
    if (idiomaIds.length > limits.max_idiomas) {
      throw new ConflictException(`Your plan allows maximum ${limits.max_idiomas} languages per subscription`);
    }

    // Get active state
    const activeState = await this.prisma.cat_Estados_Suscripcion.findFirst({
      where: { slug: 'activo' },
    });

    if (!activeState) {
      throw new Error('Configuration error: active state not found');
    }

    // Create subscription
    const subscription = await this.prisma.suscripcion.create({
      data: {
        usuario_id: userId,
        libro_id: bookId,
        estado_id: activeState.id,
        ultima_enseñanza_enviada: null,
        fecha_proximo_envio: this.calculateNextDeliveryDate(userId),
        porcentaje_avance: 0,
      },
      include: {
        libro: true,
        estado: true,
      },
    });

    // Add languages
    for (const idiomaId of idiomaIds) {
      await this.prisma.suscripcion_Idiomas.create({
        data: {
          suscripcion_id: subscription.id,
          idioma_id: idiomaId,
        },
      });
    }

    return subscription;
  }

  async getSubscriptions(userId: number) {
    return this.prisma.suscripcion.findMany({
      where: {
        usuario_id: userId,
        activo: true,
      },
      include: {
        libro: {
          include: { idioma_original: true },
        },
        estado: true,
        suscripcion_idiomas: {
          include: { idioma: true },
        },
      },
    });
  }

  async getSubscriptionById(userId: number, subscriptionId: number) {
    const subscription = await this.prisma.suscripcion.findFirst({
      where: {
        id: subscriptionId,
        usuario_id: userId,
      },
      include: {
        libro: {
          include: { idioma_original: true },
        },
        estado: true,
        suscripcion_idiomas: {
          include: { idioma: true },
        },
      },
    });

    if (!subscription) {
      throw new NotFoundException('Subscription not found');
    }

    return subscription;
  }

  async pauseSubscription(userId: number, subscriptionId: number) {
    const pausedState = await this.prisma.cat_Estados_Suscripcion.findFirst({
      where: { slug: 'pausado' },
    });

    if (!pausedState) {
      throw new Error('Configuration error: paused state not found');
    }

    return this.prisma.suscripcion.updateMany({
      where: {
        id: subscriptionId,
        usuario_id: userId,
      },
      data: {
        estado_id: pausedState.id,
      },
    });
  }

  async resumeSubscription(userId: number, subscriptionId: number) {
    const activeState = await this.prisma.cat_Estados_Suscripcion.findFirst({
      where: { slug: 'activo' },
    });

    if (!activeState) {
      throw new Error('Configuration error: active state not found');
    }

    return this.prisma.suscripcion.updateMany({
      where: {
        id: subscriptionId,
        usuario_id: userId,
      },
      data: {
        estado_id: activeState.id,
        fecha_proximo_envio: this.calculateNextDeliveryDate(userId),
      },
    });
  }

  async cancelSubscription(userId: number, subscriptionId: number) {
    return this.prisma.suscripcion.updateMany({
      where: {
        id: subscriptionId,
        usuario_id: userId,
      },
      data: {
        activo: false,
        fecha_fin: new Date(),
      },
    });
  }

  async updateSubscriptionLanguages(userId: number, subscriptionId: number, idiomaIds: number[]) {
    const limits = await this.usersService.getSubscriptionLimits(userId);
    
    if (idiomaIds.length > limits.max_idiomas) {
      throw new ConflictException(`Your plan allows maximum ${limits.max_idiomas} languages per subscription`);
    }

    // Remove existing languages
    await this.prisma.suscripcion_Idiomas.deleteMany({
      where: { suscripcion_id: subscriptionId },
    });

    // Add new languages
    for (const idiomaId of idiomaIds) {
      await this.prisma.suscripcion_Idiomas.create({
        data: {
          suscripcion_id: subscriptionId,
          idioma_id: idiomaId,
        },
      });
    }

    return this.getSubscriptionById(userId, subscriptionId);
  }

  async updateProgress(subscriptionId: number) {
    const subscription = await this.prisma.suscripcion.findUnique({
      where: { id: subscriptionId },
      include: { libro: true },
    });

    if (!subscription) {
      throw new NotFoundException('Subscription not found');
    }

    const totalTeachings = subscription.libro.cantidad_enseñanzas;
    const currentTeaching = subscription.ultima_enseñanza_enviada || 0;
    const progress = totalTeachings > 0 ? (currentTeaching / totalTeachings) * 100 : 0;

    // Check if completed
    if (currentTeaching >= totalTeachings && totalTeachings > 0) {
      const completedState = await this.prisma.cat_Estados_Suscripcion.findFirst({
        where: { slug: 'completado' },
      });

      if (completedState) {
        await this.prisma.suscripcion.update({
          where: { id: subscriptionId },
          data: {
            estado_id: completedState.id,
            porcentaje_avance: 100,
            fecha_fin: new Date(),
          },
        });
        return;
      }
    }

    await this.prisma.suscripcion.update({
      where: { id: subscriptionId },
      data: { porcentaje_avance: progress },
    });
  }

  private async calculateNextDeliveryDate(userId: number): Date {
    const user = await this.prisma.usuario.findUnique({
      where: { id: userId },
      include: { frecuencia: true },
    });

    if (!user) {
      throw new NotFoundException('User not found');
    }

    const now = new Date();
    const nextDate = new Date(now);
    
    // Add days between deliveries
    nextDate.setDate(now.getDate() + user.frecuencia.dias_entre_envios);
    
    // Set delivery hour
    nextDate.setHours(user.hora_envio, 0, 0, 0);

    // Adjust for timezone
    // This is a simplified version - in production you'd use a proper timezone library
    return nextDate;
  }

  async getNextTeaching(subscriptionId: number) {
    const subscription = await this.prisma.suscripcion.findUnique({
      where: { id: subscriptionId },
      include: { libro: true },
    });

    if (!subscription) {
      throw new NotFoundException('Subscription not found');
    }

    const nextOrder = (subscription.ultima_enseñanza_enviada || 0) + 1;

    return this.prisma.enseñanza.findFirst({
      where: {
        libro_id: subscription.libro_id,
        orden: nextOrder,
        estado: 'aprobado',
      },
    });
  }
}
