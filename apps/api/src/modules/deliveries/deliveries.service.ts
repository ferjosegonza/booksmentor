import { Injectable, Logger } from '@nestjs/common';
import { InjectQueue } from '@nestjs/bull';
import { Queue } from 'bull';
import { PrismaService } from '../../common/prisma/prisma.service';
import { SubscriptionsService } from '../subscriptions/subscriptions.service';
import { TranslationsService } from '../translations/translations.service';

interface EmailProvider {
  sendEmail(to: string, subject: string, html: string): Promise<boolean>;
  getName(): string;
}

class ResendProvider implements EmailProvider {
  private logger = new Logger(ResendProvider.name);
  private resend: any;

  constructor(private apiKey: string) {
    // Dynamically import to avoid issues if package not installed
    try {
      const Resend = require('resend');
      this.resend = new Resend(apiKey);
    } catch (error) {
      this.logger.warn('Resend package not available');
    }
  }

  getName(): string {
    return 'resend';
  }

  async sendEmail(to: string, subject: string, html: string): Promise<boolean> {
    try {
      if (!this.resend) {
        throw new Error('Resend not initialized');
      }

      await this.resend.emails.send({
        from: 'BookMentor <noreply@booksmentor.com>',
        to: [to],
        subject: subject,
        html: html,
      });

      return true;
    } catch (error) {
      this.logger.error(`Error sending email with Resend: ${error.message}`);
      return false;
    }
  }
}

class BrevoProvider implements EmailProvider {
  private logger = new Logger(BrevoProvider.name);
  private brevo: any;

  constructor(private apiKey: string) {
    try {
      const SibApiV3Sdk = require('@sendinblue/client');
      this.brevo = new SibApiV3Sdk.TransactionalEmailsApi();
      this.brevo.setApiKey(SibApiV3Sdk.TransactionalEmailsApiApiKeys.apiKey, apiKey);
    } catch (error) {
      this.logger.warn('Brevo package not available');
    }
  }

  getName(): string {
    return 'brevo';
  }

  async sendEmail(to: string, subject: string, html: string): Promise<boolean> {
    try {
      if (!this.brevo) {
        throw new Error('Brevo not initialized');
      }

      await this.brevo.sendTransacEmail({
        to: [{ email: to }],
        subject: subject,
        htmlContent: html,
        sender: { email: 'noreply@booksmentor.com', name: 'BookMentor' },
      });

      return true;
    } catch (error) {
      this.logger.error(`Error sending email with Brevo: ${error.message}`);
      return false;
    }
  }
}

class SesProvider implements EmailProvider {
  private logger = new Logger(SesProvider.name);
  private ses: any;

  constructor(
    private accessKeyId: string,
    private secretAccessKey: string,
    private region: string,
  ) {
    try {
      const { SESClient, SendEmailCommand } = require('@aws-sdk/client-ses');
      this.ses = new SESClient({
        region: region,
        credentials: {
          accessKeyId: accessKeyId,
          secretAccessKey: secretAccessKey,
        },
      });
    } catch (error) {
      this.logger.warn('AWS SES package not available');
    }
  }

  getName(): string {
    return 'ses';
  }

  async sendEmail(to: string, subject: string, html: string): Promise<boolean> {
    try {
      if (!this.ses) {
        throw new Error('SES not initialized');
      }

      const { SendEmailCommand } = require('@aws-sdk/client-ses');
      const command = new SendEmailCommand({
        Source: 'noreply@booksmentor.com',
        Destination: { ToAddresses: [to] },
        Message: {
          Subject: { Data: subject },
          Body: { Html: { Data: html } },
        },
      });

      await this.ses.send(command);
      return true;
    } catch (error) {
      this.logger.error(`Error sending email with SES: ${error.message}`);
      return false;
    }
  }
}

@Injectable()
export class DeliveriesService {
  private logger = new Logger(DeliveriesService.name);
  private emailProviders: Map<string, EmailProvider> = new Map();

  constructor(
    @InjectQueue('deliveries') private deliveriesQueue: Queue,
    private prisma: PrismaService,
    private subscriptionsService: SubscriptionsService,
    private translationsService: TranslationsService,
  ) {
    this.initializeEmailProviders();
    this.processDeliveriesQueue();
  }

  private initializeEmailProviders() {
    // Initialize Resend
    const resendKey = process.env.RESEND_API_KEY;
    if (resendKey) {
      this.emailProviders.set('resend', new ResendProvider(resendKey));
    }

    // Initialize Brevo
    const brevoKey = process.env.BREVO_API_KEY;
    if (brevoKey) {
      this.emailProviders.set('brevo', new BrevoProvider(brevoKey));
    }

    // Initialize SES
    const awsKeyId = process.env.AWS_ACCESS_KEY_ID;
    const awsSecret = process.env.AWS_SECRET_ACCESS_KEY;
    const awsRegion = process.env.AWS_REGION;
    if (awsKeyId && awsSecret && awsRegion) {
      this.emailProviders.set('ses', new SesProvider(awsKeyId, awsSecret, awsRegion));
    }

    this.logger.log(`Initialized ${this.emailProviders.size} email providers`);
  }

  private async getAvailableEmailProvider(): Promise<EmailProvider> {
    const catalog = await this.prisma.emailCatalog.findMany({
      where: {
        estado: 'activo',
        activo: true,
      },
      orderBy: { prioridad: 'asc' },
    });

    for (const entry of catalog) {
      const provider = this.emailProviders.get(entry.proveedor);
      if (provider) {
        // Check if provider is exhausted
        if (entry.limite_mensual && entry.consumo_mensual >= entry.limite_mensual) {
          await this.prisma.emailCatalog.update({
            where: { id: entry.id },
            data: { estado: 'agotado' },
          });
          continue;
        }
        if (entry.limite_diario && entry.consumo_diario >= entry.limite_diario) {
          await this.prisma.emailCatalog.update({
            where: { id: entry.id },
            data: { estado: 'agotado' },
          });
          continue;
        }
        return provider;
      }
    }

    throw new Error('No available email providers');
  }

  private async updateEmailProviderUsage(providerName: string) {
    const catalog = await this.prisma.emailCatalog.findFirst({
      where: { proveedor: providerName },
    });

    if (catalog) {
      await this.prisma.emailCatalog.update({
        where: { id: catalog.id },
        data: {
          consumo_mensual: { increment: 1 },
          consumo_diario: { increment: 1 },
        },
      });
    }
  }

  private async processDeliveriesQueue() {
    this.deliveriesQueue.process(async (job) => {
      this.logger.log(`Processing delivery job: ${job.id}`);
      
      const { subscriptionId } = job.data;
      
      try {
        await this.processScheduledDelivery(subscriptionId);
        return { success: true };
      } catch (error) {
        this.logger.error(`Error processing delivery job: ${error.message}`);
        throw error;
      }
    });
  }

  async scheduleDelivery(subscriptionId: number, deliveryDate: Date) {
    await this.deliveriesQueue.add(
      'send-delivery',
      { subscriptionId },
      {
        delay: deliveryDate.getTime() - Date.now(),
        attempts: 3,
        backoff: {
          type: 'exponential',
          delay: 5000,
        },
      },
    );
  }

  private async processScheduledDelivery(subscriptionId: number) {
    const subscription = await this.prisma.suscripcion.findUnique({
      where: { id: subscriptionId },
      include: {
        usuario: true,
        libro: true,
        suscripcion_idiomas: {
          include: { idioma: true },
        },
      },
    });

    if (!subscription || subscription.estado_id !== 1) { // 1 = Active
      this.logger.log(`Skipping delivery for subscription ${subscriptionId}: not active`);
      return;
    }

    // Get next teaching
    const nextTeaching = await this.subscriptionsService.getNextTeaching(subscriptionId);
    if (!nextTeaching) {
      this.logger.log(`No more teachings for subscription ${subscriptionId}`);
      await this.subscriptionsService.updateProgress(subscriptionId);
      return;
    }

    // Get delivery channels
    const channels = subscription.usuario.canal_entrega;

    // Process each language
    for (const subIdioma of subscription.suscripcion_idiomas) {
      const translatedText = await this.translationsService.getOrGenerateTranslation(
        nextTeaching.id,
        subIdioma.idioma_id,
      );

      if (channels === 'email' || channels === 'ambos') {
        await this.sendEmailDelivery(
          subscription.usuario.email,
          subscription.libro.titulo,
          nextTeaching,
          translatedText,
          subIdioma.idioma.nombre,
        );
      }

      if (channels === 'in_app' || channels === 'ambos') {
        await this.sendPushNotification(
          subscription.usuario.id,
          subscription.libro.titulo,
          nextTeaching.tema || 'Nueva enseñanza disponible',
        );
      }

      // Record delivery
      const deliveredState = await this.prisma.cat_Estados_Envio.findFirst({
        where: { slug: 'entregado' },
      });

      if (deliveredState) {
        await this.prisma.historial_Envios.create({
          data: {
            usuario_id: subscription.usuario_id,
            enseñanza_id: nextTeaching.id,
            idioma_id: subIdioma.idioma_id,
            estado_id: deliveredState.id,
            canal: channels,
          },
        });
      }
    }

    // Update subscription progress
    await this.prisma.suscripcion.update({
      where: { id: subscriptionId },
      data: {
        ultima_enseñanza_enviada: nextTeaching.orden,
      },
    });

    await this.subscriptionsService.updateProgress(subscriptionId);

    // Schedule next delivery
    const nextDeliveryDate = await this.calculateNextDeliveryDate(subscription.usuario_id);
    if (nextDeliveryDate) {
      await this.scheduleDelivery(subscriptionId, nextDeliveryDate);
    }
  }

  private async sendEmailDelivery(
    email: string,
    bookTitle: string,
    teaching: any,
    translatedText: string,
    languageName: string,
  ) {
    try {
      const provider = await this.getAvailableEmailProvider();
      
      const subject = `Tu enseñanza de "${bookTitle}"`;
      const html = `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
          <h2 style="color: #333;">Tu enseñanza de "${bookTitle}"</h2>
          <p style="color: #666;">Idioma: ${languageName}</p>
          <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p style="color: #333; line-height: 1.6;">${translatedText}</p>
          </div>
          <p style="color: #999; font-size: 12px;">
            Este contenido fue generado por inteligencia artificial y puede contener imprecisiones.
            No sustituye asesoramiento profesional.
          </p>
        </div>
      `;

      const success = await provider.sendEmail(email, subject, html);
      
      if (success) {
        await this.updateEmailProviderUsage(provider.getName());
      }

      return success;
    } catch (error) {
      this.logger.error(`Error sending email delivery: ${error.message}`);
      return false;
    }
  }

  private async sendPushNotification(userId: number, bookTitle: string, message: string) {
    // Implement Expo push notification
    this.logger.log(`Push notification for user ${userId}: ${message}`);
    // TODO: Implement actual Expo push notification
  }

  private async calculateNextDeliveryDate(userId: number): Promise<Date | null> {
    const user = await this.prisma.usuario.findUnique({
      where: { id: userId },
      include: { frecuencia: true },
    });

    if (!user) {
      return null;
    }

    const now = new Date();
    const nextDate = new Date(now);
    nextDate.setDate(now.getDate() + user.frecuencia.dias_entre_envios);
    nextDate.setHours(user.hora_envio, 0, 0, 0);

    return nextDate;
  }

  async getDeliveryHistory(userId: number) {
    return this.prisma.historial_Envios.findMany({
      where: { usuario_id: userId },
      include: {
        enseñanza: {
          include: { libro: true },
        },
        idioma: true,
        estado: true,
      },
      orderBy: { fecha_envio: 'desc' },
    });
  }

  async rescheduleAllDeliveries() {
    const activeSubscriptions = await this.prisma.suscripcion.findMany({
      where: {
        estado_id: 1, // Active
        activo: true,
        fecha_proximo_envio: { gte: new Date() },
      },
    });

    for (const subscription of activeSubscriptions) {
      if (subscription.fecha_proximo_envio) {
        await this.scheduleDelivery(subscription.id, subscription.fecha_proximo_envio);
      }
    }

    return { rescheduled: activeSubscriptions.length };
  }
}
