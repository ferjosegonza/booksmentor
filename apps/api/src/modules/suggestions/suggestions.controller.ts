import { Controller, Get, Post, Put, Body, Param, UseGuards, Request } from '@nestjs/common';
import { SuggestionsService } from './suggestions.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@Controller('suggestions')
export class SuggestionsController {
  constructor(private suggestionsService: SuggestionsService) {}

  @Post()
  @UseGuards(JwtAuthGuard)
  createSuggestion(@Request() req, @Body() body: {
    tipo_id: number;
    libro_sugerido?: string;
    mensaje: string;
  }) {
    return this.suggestionsService.createSuggestion({
      usuario_id: req.user.id,
      email: req.user.email,
      ...body,
    });
  }

  @Post('anonymous')
  createAnonymousSuggestion(@Body() body: {
    email: string;
    tipo_id: number;
    libro_sugerido?: string;
    mensaje: string;
  }) {
    return this.suggestionsService.createSuggestion(body);
  }

  @Get()
  @UseGuards(JwtAuthGuard)
  getSuggestions(@Request() req) {
    return this.suggestionsService.getSuggestions(req.user.id);
  }

  @Get('all')
  getAllSuggestions() {
    return this.suggestionsService.getSuggestions();
  }

  @Get('types')
  getSuggestionTypes() {
    return this.suggestionsService.getSuggestionTypes();
  }

  @Get('unread')
  getUnreadSuggestions() {
    return this.suggestionsService.getUnreadSuggestions();
  }

  @Get('unattended')
  getUnattendedSuggestions() {
    return this.suggestionsService.getUnattendedSuggestions();
  }

  @Get(':id')
  getSuggestionById(@Param('id') id: string) {
    return this.suggestionsService.getSuggestionById(parseInt(id));
  }

  @Put(':id/read')
  markAsRead(@Param('id') id: string) {
    return this.suggestionsService.markAsRead(parseInt(id));
  }

  @Put(':id/attend')
  markAsAttended(@Param('id') id: string, @Body() body: { respuesta: string }) {
    return this.suggestionsService.markAsAttended(parseInt(id), body.respuesta);
  }
}
