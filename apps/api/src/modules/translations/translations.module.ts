import { Module } from '@nestjs/common';
import { TranslationsService } from './translations.service.js';
import { TranslationsController } from './translations.controller.js';
import { AiModule } from '../ai/ai.module.js';
import { AuthModule } from '../auth/auth.module.js';

@Module({
  imports: [AiModule, AuthModule],
  controllers: [TranslationsController],
  providers: [TranslationsService],
  exports: [TranslationsService],
})
export class TranslationsModule {}
