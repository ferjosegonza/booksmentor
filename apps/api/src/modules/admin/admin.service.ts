import { Injectable, NotFoundException, ForbiddenException } from '@nestjs/common';
import { PrismaService } from '../../common/prisma/prisma.service';
import { AiService } from '../ai/ai.service';
import { BooksService } from '../books/books.service';

@Injectable()
export class AdminService {
  constructor(
    private prisma: PrismaService,
    private aiService: AiService,
    private booksService: BooksService,
  ) {}

  private async checkAdminRole(userId: number) {
    const user = await this.prisma.usuario.findUnique({
      where: { id: userId },
    });

    if (!user || user.role !== 'administrador') {
      throw new ForbiddenException('Admin access required');
    }
  }

  // Teaching Review and Approval
  async getPendingTeachings() {
    return this.prisma.enseñanza.findMany({
      where: { estado: 'generado_por_ia_pendiente_revision' },
      include: {
        libro: {
          include: { idioma_original: true },
        },
      },
      orderBy: { creado_en: 'asc' },
    });
  }

  async approveTeaching(teachingId: number, adminId: number) {
    await this.checkAdminRole(adminId);

    const teaching = await this.prisma.enseñanza.findUnique({
      where: { id: teachingId },
      include: { libro: true },
    });

    if (!teaching) {
      throw new NotFoundException('Teaching not found');
    }

    // Update teaching status
    const updatedTeaching = await this.prisma.enseñanza.update({
      where: { id: teachingId },
      data: {
        estado: 'aprobado',
        fecha_aprobacion: new Date(),
        aprobado_por: adminId,
      },
    });

    // Check if all teachings for this book are approved
    const allTeachings = await this.prisma.enseñanza.findMany({
      where: { libro_id: teaching.libro_id },
    });

    const allApproved = allTeachings.every(t => t.estado === 'aprobado');

    if (allApproved) {
      await this.prisma.libro.update({
        where: { id: teaching.libro_id },
        data: { estado: 'aprobado' },
      });
    }

    return updatedTeaching;
  }

  async rejectTeaching(teachingId: number, adminId: number, reason?: string) {
    await this.checkAdminRole(adminId);

    const teaching = await this.prisma.enseñanza.findUnique({
      where: { id: teachingId },
    });

    if (!teaching) {
      throw new NotFoundException('Teaching not found');
    }

    return this.prisma.enseñanza.update({
      where: { id: teachingId },
      data: {
        estado: 'rechazado',
      },
    });
  }

  async regenerateTeaching(teachingId: number, adminId: number) {
    await this.checkAdminRole(adminId);

    const teaching = await this.prisma.enseñanza.findUnique({
      where: { id: teachingId },
      include: { libro: true },
    });

    if (!teaching) {
      throw new NotFoundException('Teaching not found');
    }

    // Generate new teaching
    const newTeachingText = await this.aiService.generateTeaching(
      teaching.libro.titulo,
      teaching.libro.autor,
      teaching.tema || undefined,
    );

    // Update teaching
    return this.prisma.enseñanza.update({
      where: { id: teachingId },
      data: {
        texto_original: newTeachingText,
        estado: 'generado_por_ia_pendiente_revision',
        version: { increment: 1 },
        fecha_generacion: new Date(),
      },
    });
  }

  async editTeaching(teachingId: number, adminId: number, newText: string) {
    await this.checkAdminRole(adminId);

    const teaching = await this.prisma.enseñanza.findUnique({
      where: { id: teachingId },
    });

    if (!teaching) {
      throw new NotFoundException('Teaching not found');
    }

    return this.prisma.enseñanza.update({
      where: { id: teachingId },
      data: {
        texto_original: newText,
        estado: 'aprobado',
        fecha_aprobacion: new Date(),
        aprobado_por: adminId,
        version: { increment: 1 },
      },
    });
  }

  // Catalog Management
  async getAllCatalogs() {
    const [planes, idiomas, frecuencias, estadosSuscripcion, estadosEnvio, tags, tiposSugerencia] = await Promise.all([
      this.prisma.cat_Planes.findMany(),
      this.prisma.cat_Idiomas.findMany(),
      this.prisma.cat_Frecuencias.findMany(),
      this.prisma.cat_Estados_Suscripcion.findMany(),
      this.prisma.cat_Estados_Envio.findMany(),
      this.prisma.cat_Tags.findMany(),
      this.prisma.cat_Tipos_Sugerencia.findMany(),
    ]);

    return {
      planes,
      idiomas,
      frecuencias,
      estadosSuscripcion,
      estadosEnvio,
      tags,
      tiposSugerencia,
    };
  }

  async updateCatalogItem(catalog: string, id: number, data: any) {
    await this.checkAdminRole(0); // Will be implemented with proper admin check

    const catalogMap: any = {
      planes: 'cat_Planes',
      idiomas: 'cat_Idiomas',
      frecuencias: 'cat_Frecuencias',
      estadosSuscripcion: 'cat_Estados_Suscripcion',
      estadosEnvio: 'cat_Estados_Envio',
      tags: 'cat_Tags',
      tiposSugerencia: 'cat_Tipos_Sugerencia',
    };

    const modelName = catalogMap[catalog];
    if (!modelName) {
      throw new NotFoundException('Catalog not found');
    }

    return this.prisma[modelName].update({
      where: { id },
      data,
    });
  }

  // Provider Management
  async getProviderStatus() {
    const [aiProviders, emailProviders, paymentProviders] = await Promise.all([
      this.prisma.aiCatalog.findMany(),
      this.prisma.emailCatalog.findMany(),
      this.prisma.paymentProviders.findMany(),
    ]);

    return {
      ai: aiProviders,
      email: emailProviders,
      payments: paymentProviders,
    };
  }

  async updateAiProvider(id: number, data: any) {
    return this.prisma.aiCatalog.update({
      where: { id },
      data,
    });
  }

  async updateEmailProvider(id: number, data: any) {
    return this.prisma.emailCatalog.update({
      where: { id },
      data,
    });
  }

  async updatePaymentProvider(id: number, data: any) {
    return this.prisma.paymentProviders.update({
      where: { id },
      data,
    });
  }

  // Dashboard Stats
  async getDashboardStats() {
    const [
      totalUsers,
      activeUsers,
      totalBooks,
      approvedBooks,
      pendingTeachings,
      totalSubscriptions,
      activeSubscriptions,
      todayDeliveries,
      aiUsage,
      emailUsage,
    ] = await Promise.all([
      this.prisma.usuario.count(),
      this.prisma.usuario.count({ where: { activo: true } }),
      this.prisma.libro.count(),
      this.prisma.libro.count({ where: { estado: 'aprobado' } }),
      this.prisma.enseñanza.count({ where: { estado: 'generado_por_ia_pendiente_revision' } }),
      this.prisma.suscripcion.count(),
      this.prisma.suscripcion.count({ where: { estado_id: 1, activo: true } }),
      this.prisma.historial_Envios.count({
        where: {
          fecha_envio: {
            gte: new Date(new Date().setHours(0, 0, 0, 0)),
          },
        },
      }),
      this.prisma.auditoria_IA.aggregate({
        _sum: { cuota_usada: true },
      }),
      this.prisma.emailCatalog.aggregate({
        _sum: { consumo_mensual: true },
      }),
    ]);

    return {
      users: {
        total: totalUsers,
        active: activeUsers,
      },
      books: {
        total: totalBooks,
        approved: approvedBooks,
      },
      teachings: {
        pending: pendingTeachings,
      },
      subscriptions: {
        total: totalSubscriptions,
        active: activeSubscriptions,
      },
      deliveries: {
        today: todayDeliveries,
      },
      usage: {
        ai: aiUsage._sum.cuota_usada || 0,
        email: emailUsage._sum.consumo_mensual || 0,
      },
    };
  }

  // User Management
  async getAllUsers() {
    return this.prisma.usuario.findMany({
      include: {
        plan: true,
        frecuencia: true,
      },
      orderBy: { fecha_registro: 'desc' },
    });
  }

  async updateUserRole(userId: number, role: string) {
    return this.prisma.usuario.update({
      where: { id: userId },
      data: { role },
    });
  }

  async updateUserPlan(userId: number, planId: number) {
    return this.prisma.usuario.update({
      where: { id: userId },
      data: { plan_id: planId },
      include: { plan: true },
    });
  }

  async deactivateUser(userId: number) {
    return this.prisma.usuario.update({
      where: { id: userId },
      data: { activo: false },
    });
  }
}
