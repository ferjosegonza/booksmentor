import { Module } from '@nestjs/common';
import { TranslationsService } from './translations.service.js';
import { TranslationsController } from './translations.controller.js';
import { AiModule } from '../ai/ai.module.js';

@Module({
  imports: [AiModule],
  controllers: [TranslationsController],
  providers: [TranslationsService],
  exports: [TranslationsService],
})
export class TranslationsModule {}
