import { Controller, Get, Put, Body, UseGuards, Request } from '@nestjs/common';
import { UsersService } from './users.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@Controller('users')
@UseGuards(JwtAuthGuard)
export class UsersController {
  constructor(private usersService: UsersService) {}

  @Get('profile')
  getProfile(@Request() req) {
    return this.usersService.findById(req.user.id);
  }

  @Put('profile')
  updateProfile(@Request() req, @Body() body: { nombre?: string; zona_horaria?: string; hora_envio?: number }) {
    return this.usersService.updateProfile(req.user.id, body);
  }

  @Put('preferences')
  updatePreferences(
    @Request() req,
    @Body() body: {
      frecuencia_id?: number;
      canal_entrega?: string;
      zona_horaria?: string;
      hora_envio?: number;
    },
  ) {
    return this.usersService.updatePreferences(req.user.id, body);
  }

  @Get('limits')
  getSubscriptionLimits(@Request() req) {
    return this.usersService.getSubscriptionLimits(req.user.id);
  }
}
