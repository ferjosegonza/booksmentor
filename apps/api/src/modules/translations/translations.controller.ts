import { Controller, Get, Post, Put, Param, UseGuards, Request } from '@nestjs/common';
import { TranslationsService } from './translations.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@Controller('translations')
@UseGuards(JwtAuthGuard)
export class TranslationsController {
  constructor(private translationsService: TranslationsService) {}

  @Get('teaching/:teachingId/language/:languageId')
  getOrGenerateTranslation(@Param('teachingId') teachingId: string, @Param('languageId') languageId: string) {
    return this.translationsService.getOrGenerateTranslation(parseInt(teachingId), parseInt(languageId));
  }

  @Get('by-id/:id')
  getTranslationById(@Param('id') id: string) {
    return this.translationsService.getTranslationById(parseInt(id));
  }

  @Get('by-teaching/:teachingId')
  getTranslationsByTeaching(@Param('teachingId') teachingId: string) {
    return this.translationsService.getTranslationsByTeaching(parseInt(teachingId));
  }

  @Get('by-language/:languageId')
  getTranslationsByLanguage(@Param('languageId') languageId: string) {
    return this.translationsService.getTranslationsByLanguage(parseInt(languageId));
  }

  @Put(':id/regenerate')
  regenerateTranslation(@Param('id') id: string) {
    return this.translationsService.regenerateTranslation(parseInt(id));
  }

  @Get('stats')
  getTranslationStats() {
    return this.translationsService.getTranslationStats();
  }
}
