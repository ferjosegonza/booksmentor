import { Controller, Get, Post, Put, Delete, Body, Param, UseGuards, Request } from '@nestjs/common';
import { SubscriptionsService } from './subscriptions.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@Controller('subscriptions')
@UseGuards(JwtAuthGuard)
export class SubscriptionsController {
  constructor(private subscriptionsService: SubscriptionsService) {}

  @Post()
  createSubscription(@Request() req, @Body() body: { bookId: number; idiomaIds: number[] }) {
    return this.subscriptionsService.createSubscription(req.user.id, body.bookId, body.idiomaIds);
  }

  @Get()
  getSubscriptions(@Request() req) {
    return this.subscriptionsService.getSubscriptions(req.user.id);
  }

  @Get(':id')
  getSubscriptionById(@Request() req, @Param('id') id: string) {
    return this.subscriptionsService.getSubscriptionById(req.user.id, parseInt(id));
  }

  @Put(':id/pause')
  pauseSubscription(@Request() req, @Param('id') id: string) {
    return this.subscriptionsService.pauseSubscription(req.user.id, parseInt(id));
  }

  @Put(':id/resume')
  resumeSubscription(@Request() req, @Param('id') id: string) {
    return this.subscriptionsService.resumeSubscription(req.user.id, parseInt(id));
  }

  @Delete(':id')
  cancelSubscription(@Request() req, @Param('id') id: string) {
    return this.subscriptionsService.cancelSubscription(req.user.id, parseInt(id));
  }

  @Put(':id/languages')
  updateSubscriptionLanguages(@Request() req, @Param('id') id: string, @Body() body: { idiomaIds: number[] }) {
    return this.subscriptionsService.updateSubscriptionLanguages(req.user.id, parseInt(id), body.idiomaIds);
  }

  @Get(':id/next-teaching')
  getNextTeaching(@Param('id') id: string) {
    return this.subscriptionsService.getNextTeaching(parseInt(id));
  }
}
