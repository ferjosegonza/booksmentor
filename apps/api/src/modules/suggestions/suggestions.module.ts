import { Module } from '@nestjs/common';
import { SuggestionsService } from './suggestions.service.js';
import { SuggestionsController } from './suggestions.controller.js';

@Module({
  controllers: [SuggestionsController],
  providers: [SuggestionsService],
  exports: [SuggestionsService],
})
export class SuggestionsModule {}
