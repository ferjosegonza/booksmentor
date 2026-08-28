import { Controller, Get, Post, Put, Body, Param, Headers, UseGuards, Request } from '@nestjs/common';
import { PaymentsService } from './payments.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@Controller('payments')
export class PaymentsController {
  constructor(private paymentsService: PaymentsService) {}

  @Post('create-subscription')
  @UseGuards(JwtAuthGuard)
  createSubscription(@Request() req, @Body() body: { planId: string; region?: string }) {
    return this.paymentsService.createSubscription(body.planId, req.user.id, body.region);
  }

  @Post('webhook/:provider')
  async handleWebhook(
    @Param('provider') provider: string,
    @Body() data: any,
    @Headers('stripe-signature') stripeSignature?: string,
  ) {
    const signature = stripeSignature || '';
    return this.paymentsService.handleWebhook(provider, data, signature);
  }

  @Put('cancel-subscription')
  @UseGuards(JwtAuthGuard)
  cancelSubscription(@Body() body: { subscriptionId: string; providerName: string }) {
    return this.paymentsService.cancelSubscription(body.subscriptionId, body.providerName);
  }

  @Post('refund')
  @UseGuards(JwtAuthGuard)
  refund(@Body() body: { transactionId: string; providerName: string; amount?: number }) {
    return this.paymentsService.refund(body.transactionId, body.providerName, body.amount);
  }

  @Get('providers')
  getAvailableProviders() {
    return this.paymentsService.getAvailableProviders();
  }

  @Put('upgrade-plan')
  @UseGuards(JwtAuthGuard)
  upgradeUserPlan(@Request() req, @Body() body: { newPlanId: number }) {
    return this.paymentsService.upgradeUserPlan(req.user.id, body.newPlanId);
  }
}
