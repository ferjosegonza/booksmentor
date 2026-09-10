import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { BullModule } from '@nestjs/bull';
import { PrismaModule } from './common/prisma/prisma.module.js';

// Modules
import { AuthModule } from './modules/auth/auth.module.js';
import { UsersModule } from './modules/users/users.module.js';
import { BooksModule } from './modules/books/books.module.js';
import { AiModule } from './modules/ai/ai.module.js';
import { TranslationsModule } from './modules/translations/translations.module.js';
import { SubscriptionsModule } from './modules/subscriptions/subscriptions.module.js';
import { DeliveriesModule } from './modules/deliveries/deliveries.module.js';
import { PaymentsModule } from './modules/payments/payments.module.js';
import { SuggestionsModule } from './modules/suggestions/suggestions.module.js';
import { AdminModule } from './modules/admin/admin.module.js';

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: '.env',
    }),
    PrismaModule,
    ...(process.env.USE_REDIS === 'true'
      ? [
          BullModule.forRoot({
            redis: {
              host: process.env.REDIS_HOST || 'localhost',
              port: parseInt(process.env.REDIS_PORT ?? '6379', 10),
              password: process.env.REDIS_PASSWORD || undefined,
            },
          }),
        ]
      : []),
    AuthModule,
    UsersModule,
    BooksModule,
    AiModule,
    TranslationsModule,
    SubscriptionsModule,
    DeliveriesModule,
    PaymentsModule,
    SuggestionsModule,
    AdminModule,
  ],
})
export class AppModule {}
