import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../common/prisma/prisma.service';

interface PaymentProvider {
  createSubscription(planId: string, userId: number): Promise<any>;
  cancelSubscription(subscriptionId: string): Promise<boolean>;
  handleWebhook(data: any, signature: string): Promise<any>;
  refund(transactionId: string, amount?: number): Promise<boolean>;
  getName(): string;
}

class MercadoPagoProvider implements PaymentProvider {
  private logger = new Logger(MercadoPagoProvider.name);
  private mercadopago: any;

  constructor(private accessToken: string) {
    try {
      const mercadopago = require('mercadopago');
      mercadopago.configure({ access_token: accessToken });
      this.mercadopago = mercadopago;
    } catch (error) {
      this.logger.warn('MercadoPago package not available');
    }
  }

  getName(): string {
    return 'mercadopago';
  }

  async createSubscription(planId: string, userId: number): Promise<any> {
    try {
      if (!this.mercadopago) {
        throw new Error('MercadoPago not initialized');
      }

      const preference = await this.mercadopago.preferences.create({
        items: [
          {
            title: 'BookMentor Subscription',
            quantity: 1,
            currency_id: 'ARS',
            unit_price: 100, // This should come from plan configuration
          },
        ],
        back_urls: {
          success: 'http://localhost:3000/payment/success',
          failure: 'http://localhost:3000/payment/failure',
          pending: 'http://localhost:3000/payment/pending',
        },
        auto_return: 'approved',
        metadata: {
          userId: userId,
          planId: planId,
        },
      });

      return { init_point: preference.body.init_point };
    } catch (error) {
      this.logger.error(`Error creating MercadoPago subscription: ${error.message}`);
      throw error;
    }
  }

  async cancelSubscription(subscriptionId: string): Promise<boolean> {
    try {
      if (!this.mercadopago) {
        throw new Error('MercadoPago not initialized');
      }

      await this.mercadopago.preapproval.cancel(subscriptionId);
      return true;
    } catch (error) {
      this.logger.error(`Error canceling MercadoPago subscription: ${error.message}`);
      return false;
    }
  }

  async handleWebhook(data: any, signature: string): Promise<any> {
    // Implement MercadoPago webhook verification
    this.logger.log(`MercadoPago webhook received: ${JSON.stringify(data)}`);
    return { processed: true };
  }

  async refund(transactionId: string, amount?: number): Promise<boolean> {
    try {
      if (!this.mercadopago) {
        throw new Error('MercadoPago not initialized');
      }

      await this.mercadopago.payments.refund(transactionId);
      return true;
    } catch (error) {
      this.logger.error(`Error refunding MercadoPago transaction: ${error.message}`);
      return false;
    }
  }
}

class PayPalProvider implements PaymentProvider {
  private logger = new Logger(PayPalProvider.name);
  private paypal: any;

  constructor(
    private clientId: string,
    private clientSecret: string,
  ) {
    try {
      const paypal = require('@paypal/checkout-server-sdk');
      this.paypal = paypal;
    } catch (error) {
      this.logger.warn('PayPal package not available');
    }
  }

  getName(): string {
    return 'paypal';
  }

  async createSubscription(planId: string, userId: number): Promise<any> {
    try {
      if (!this.paypal) {
        throw new Error('PayPal not initialized');
      }

      // Implement PayPal subscription creation
      // This is a simplified version
      return { approvalUrl: 'https://paypal.com/approve' };
    } catch (error) {
      this.logger.error(`Error creating PayPal subscription: ${error.message}`);
      throw error;
    }
  }

  async cancelSubscription(subscriptionId: string): Promise<boolean> {
    try {
      // Implement PayPal subscription cancellation
      return true;
    } catch (error) {
      this.logger.error(`Error canceling PayPal subscription: ${error.message}`);
      return false;
    }
  }

  async handleWebhook(data: any, signature: string): Promise<any> {
    // Implement PayPal webhook verification
    this.logger.log(`PayPal webhook received: ${JSON.stringify(data)}`);
    return { processed: true };
  }

  async refund(transactionId: string, amount?: number): Promise<boolean> {
    try {
      // Implement PayPal refund
      return true;
    } catch (error) {
      this.logger.error(`Error refunding PayPal transaction: ${error.message}`);
      return false;
    }
  }
}

class StripeProvider implements PaymentProvider {
  private logger = new Logger(StripeProvider.name);
  private stripe: any;

  constructor(private secretKey: string) {
    try {
      const stripe = require('stripe');
      this.stripe = stripe(secretKey);
    } catch (error) {
      this.logger.warn('Stripe package not available');
    }
  }

  getName(): string {
    return 'stripe';
  }

  async createSubscription(planId: string, userId: number): Promise<any> {
    try {
      if (!this.stripe) {
        throw new Error('Stripe not initialized');
      }

      // Implement Stripe subscription creation
      // This is a simplified version
      return { checkoutUrl: 'https://stripe.com/checkout' };
    } catch (error) {
      this.logger.error(`Error creating Stripe subscription: ${error.message}`);
      throw error;
    }
  }

  async cancelSubscription(subscriptionId: string): Promise<boolean> {
    try {
      if (!this.stripe) {
        throw new Error('Stripe not initialized');
      }

      await this.stripe.subscriptions.cancel(subscriptionId);
      return true;
    } catch (error) {
      this.logger.error(`Error canceling Stripe subscription: ${error.message}`);
      return false;
    }
  }

  async handleWebhook(data: any, signature: string): Promise<any> {
    try {
      if (!this.stripe) {
        throw new Error('Stripe not initialized');
      }

      const webhookSecret = process.env.STRIPE_WEBHOOK_SECRET;
      const event = this.stripe.webhooks.constructEvent(data, signature, webhookSecret);
      
      this.logger.log(`Stripe webhook received: ${event.type}`);
      return { processed: true, eventType: event.type };
    } catch (error) {
      this.logger.error(`Error processing Stripe webhook: ${error.message}`);
      throw error;
    }
  }

  async refund(transactionId: string, amount?: number): Promise<boolean> {
    try {
      if (!this.stripe) {
        throw new Error('Stripe not initialized');
      }

      await this.stripe.refunds.create({
        payment_intent: transactionId,
        amount: amount,
      });
      return true;
    } catch (error) {
      this.logger.error(`Error refunding Stripe transaction: ${error.message}`);
      return false;
    }
  }
}

@Injectable()
export class PaymentsService {
  private logger = new Logger(PaymentsService.name);
  private providers: Map<string, PaymentProvider> = new Map();

  constructor(private prisma: PrismaService) {
    this.initializeProviders();
  }

  private initializeProviders() {
    // Initialize MercadoPago
    const mpToken = process.env.MERCADOPAGO_ACCESS_TOKEN;
    if (mpToken) {
      this.providers.set('mercadopago', new MercadoPagoProvider(mpToken));
    }

    // Initialize PayPal
    const paypalClientId = process.env.PAYPAL_CLIENT_ID;
    const paypalSecret = process.env.PAYPAL_CLIENT_SECRET;
    if (paypalClientId && paypalSecret) {
      this.providers.set('paypal', new PayPalProvider(paypalClientId, paypalSecret));
    }

    // Initialize Stripe
    const stripeKey = process.env.STRIPE_SECRET_KEY;
    if (stripeKey) {
      this.providers.set('stripe', new StripeProvider(stripeKey));
    }

    this.logger.log(`Initialized ${this.providers.size} payment providers`);
  }

  private async getActiveProvider(region?: string): Promise<PaymentProvider> {
    const catalog = await this.prisma.paymentProviders.findMany({
      where: {
        activo: true,
        ...(region && { region }),
      },
      orderBy: { prioridad: 'asc' },
    });

    for (const entry of catalog) {
      const provider = this.providers.get(entry.proveedor);
      if (provider) {
        return provider;
      }
    }

    throw new Error('No available payment providers');
  }

  async createSubscription(planId: string, userId: number, region?: string) {
    try {
      const provider = await this.getActiveProvider(region);
      return provider.createSubscription(planId, userId);
    } catch (error) {
      this.logger.error(`Error creating subscription: ${error.message}`);
      throw error;
    }
  }

  async cancelSubscription(subscriptionId: string, providerName: string) {
    try {
      const provider = this.providers.get(providerName);
      if (!provider) {
        throw new Error(`Provider ${providerName} not found`);
      }
      return provider.cancelSubscription(subscriptionId);
    } catch (error) {
      this.logger.error(`Error canceling subscription: ${error.message}`);
      throw error;
    }
  }

  async handleWebhook(providerName: string, data: any, signature: string) {
    try {
      const provider = this.providers.get(providerName);
      if (!provider) {
        throw new Error(`Provider ${providerName} not found`);
      }
      return provider.handleWebhook(data, signature);
    } catch (error) {
      this.logger.error(`Error handling webhook: ${error.message}`);
      throw error;
    }
  }

  async refund(transactionId: string, providerName: string, amount?: number) {
    try {
      const provider = this.providers.get(providerName);
      if (!provider) {
        throw new Error(`Provider ${providerName} not found`);
      }
      return provider.refund(transactionId, amount);
    } catch (error) {
      this.logger.error(`Error processing refund: ${error.message}`);
      throw error;
    }
  }

  async getAvailableProviders() {
    return this.prisma.paymentProviders.findMany({
      where: { activo: true },
      orderBy: { prioridad: 'asc' },
    });
  }

  async upgradeUserPlan(userId: number, newPlanId: number) {
    return this.prisma.usuario.update({
      where: { id: userId },
      data: { plan_id: newPlanId },
      include: { plan: true },
    });
  }
}
