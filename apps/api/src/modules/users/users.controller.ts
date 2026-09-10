import { Controller, Get, Put, Body, UseGuards, Request } from "@nestjs/common";
import { UsersService } from "./users.service.js";
import { JwtAuthGuard } from "../auth/guards/jwt-auth.guard.js";

@Controller("users")
@UseGuards(new JwtAuthGuard())
export class UsersController {
  constructor(private usersService: UsersService) {}

  @Get("profile")
  getProfile(@Request() req: any) {
    return this.usersService.findById(req.user.id);
  }

  @Put("profile")
  updateProfile(
    @Request() req: any,
    @Body()
    body: { nombre?: string; zona_horaria?: string; hora_envio?: number },
  ) {
    return this.usersService.updateProfile(req.user.id, body);
  }

  @Put("preferences")
  updatePreferences(
    @Request() req: any,
    @Body()
    body: {
      frecuencia_id?: number;
      canal_entrega?: string;
      zona_horaria?: string;
      hora_envio?: number;
    },
  ) {
    return this.usersService.updatePreferences(req.user.id, body);
  }

  @Get("limits")
  getSubscriptionLimits(@Request() req: any) {
    return this.usersService.getSubscriptionLimits(req.user.id);
  }
}
