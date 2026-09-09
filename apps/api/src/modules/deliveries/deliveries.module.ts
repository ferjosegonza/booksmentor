import { Module } from '@nestjs/common';
import { BullModule } from '@nestjs/bull';
import { DeliveriesService } from './deliveries.service';
import { DeliveriesController } from './deliveries.controller';
import { SubscriptionsModule } from '../subscriptions/subscriptions.module';
import { TranslationsModule } from '../translations/translations.module';

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
