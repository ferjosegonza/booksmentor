import { Controller, Post, Body, UseGuards, Request } from '@nestjs/common';
import { AiService } from './ai.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@Controller('ai')
@UseGuards(JwtAuthGuard)
export class AiController {
  constructor(private aiService: AiService) {}

  @Post('generate-teaching')
  generateTeaching(@Body() body: { bookTitle: string; bookAuthor: string; bookTheme?: string }) {
    return this.aiService.generateTeaching(body.bookTitle, body.bookAuthor, body.bookTheme);
  }

  @Post('translate')
  translate(@Body() body: { text: string; targetLanguage: string }) {
    return this.aiService.translate(body.text, body.targetLanguage);
  }

  @Post('regenerate-teaching')
  regenerateTeaching(@Body() body: { bookId: number; teachingOrder: number }) {
    return this.aiService.regenerateTeaching(body.bookId, body.teachingOrder);
  }

  @Post('check-renew-providers')
  checkAndRenewProviders() {
    return this.aiService.checkAndRenewProviders();
  }
}
