import { Module } from '@nestjs/common';
import { BullModule } from '@nestjs/bull';
import { DeliveriesService } from './deliveries.service.js';
import { DeliveriesController } from './deliveries.controller.js';
import { SubscriptionsModule } from '../subscriptions/subscriptions.module.js';
import { TranslationsModule } from '../translations/translations.module.js';

@Module({
  imports: [
    SubscriptionsModule,
    TranslationsModule,
    ...(process.env.USE_REDIS === 'true'
      ? [
          BullModule.registerQueue({
            name: 'deliveries',
          }),
        ]
      : []),
  ],
  controllers: [DeliveriesController],
  providers: [DeliveriesService],
  exports: [DeliveriesService],
})
export class DeliveriesModule {}
