import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { BullModule } from '@nestjs/bull';
import { PrismaService } from './common/prisma/prisma.service';

// Modules
import { AuthModule } from './modules/auth/auth.module';
import { UsersModule } from './modules/users/users.module';
import { BooksModule } from './modules/books/books.module';
import { AiModule } from './modules/ai/ai.module';
import { TranslationsModule } from './modules/translations/translations.module';
import { SubscriptionsModule } from './modules/subscriptions/subscriptions.module';
import { DeliveriesModule } from './modules/deliveries/deliveries.module';
import { PaymentsModule } from './modules/payments/payments.module';
import { SuggestionsModule } from './modules/suggestions/suggestions.module';
import { AdminModule } from './modules/admin/admin.module';

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: '.env',
    }),
    BullModule.forRoot({
      redis: {
        host: process.env.REDIS_HOST || 'localhost',
        port: parseInt(process.env.REDIS_PORT) || 6379,
        password: process.env.REDIS_PASSWORD || undefined,
      },
    }),
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
  providers: [PrismaService],
})
export class AppModule {}
