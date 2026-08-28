import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../common/prisma/prisma.service';

@Injectable()
export class UsersService {
  constructor(private prisma: PrismaService) {}

  async findByEmail(email: string) {
    return this.prisma.usuario.findUnique({
      where: { email },
      include: { plan: true, frecuencia: true },
    });
  }

  async findById(id: number) {
    return this.prisma.usuario.findUnique({
      where: { id },
      include: { plan: true, frecuencia: true },
    });
  }

  async updateProfile(userId: number, data: { nombre?: string; zona_horaria?: string; hora_envio?: number }) {
    return this.prisma.usuario.update({
      where: { id: userId },
      data,
      include: { plan: true, frecuencia: true },
    });
  }

  async updatePreferences(
    userId: number,
    data: {
      frecuencia_id?: number;
      canal_entrega?: string;
      zona_horaria?: string;
      hora_envio?: number;
    },
  ) {
    return this.prisma.usuario.update({
      where: { id: userId },
      data,
      include: { plan: true, frecuencia: true },
    });
  }

  async getSubscriptionLimits(userId: number) {
    const user = await this.prisma.usuario.findUnique({
      where: { id: userId },
      include: { plan: true },
    });

    if (!user) {
      throw new NotFoundException('User not found');
    }

    const activeSubscriptions = await this.prisma.suscripcion.count({
      where: {
        usuario_id: userId,
        estado_id: 1, // Activo
        activo: true,
      },
    });

    return {
      max_libros: user.plan.max_libros,
      max_idiomas: user.plan.max_idiomas,
      permite_audio: user.plan.permite_audio,
      current_libros: activeSubscriptions,
      can_add_more: activeSubscriptions < user.plan.max_libros,
    };
  }
}
