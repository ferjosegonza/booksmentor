import { Controller, Get, Post, Param, UseGuards, Request } from '@nestjs/common';
import { DeliveriesService } from './deliveries.service.js';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard.js';

@Controller('deliveries')
@UseGuards(new JwtAuthGuard())
export class DeliveriesController {
  constructor(private deliveriesService: DeliveriesService) {}

  @Get('history')
  getDeliveryHistory(@Request() req: any) {
    return this.deliveriesService.getDeliveryHistory(req.user.id);
  }

  @Post('reschedule-all')
  rescheduleAllDeliveries() {
    return this.deliveriesService.rescheduleAllDeliveries();
  }

  @Post(':subscriptionId/process-now')
  processDeliveryNow(@Param('subscriptionId') subscriptionId: string) {
    return this.deliveriesService.processDeliveryNow(parseInt(subscriptionId));
  }
}
