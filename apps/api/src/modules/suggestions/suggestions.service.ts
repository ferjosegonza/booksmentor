import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../common/prisma/prisma.service';

@Injectable()
export class SuggestionsService {
  constructor(private prisma: PrismaService) {}

  async createSuggestion(data: {
    usuario_id?: number;
    email?: string;
    tipo_id: number;
    libro_sugerido?: string;
    mensaje: string;
  }) {
    return this.prisma.sugerencia.create({
      data,
      include: {
        tipo: true,
        usuario: true,
      },
    });
  }

  async getSuggestions(userId?: number) {
    const where = userId ? { usuario_id: userId } : {};
    
    return this.prisma.sugerencia.findMany({
      where,
      include: {
        tipo: true,
        usuario: true,
      },
      orderBy: { fecha_envio: 'desc' },
    });
  }

  async getSuggestionById(id: number) {
    const suggestion = await this.prisma.sugerencia.findUnique({
      where: { id },
      include: {
        tipo: true,
        usuario: true,
      },
    });

    if (!suggestion) {
      throw new NotFoundException('Suggestion not found');
    }

    return suggestion;
  }

  async markAsRead(id: number) {
    return this.prisma.sugerencia.update({
      where: { id },
      data: { leido: true },
    });
  }

  async markAsAttended(id: number, respuesta: string) {
    return this.prisma.sugerencia.update({
      where: { id },
      data: {
        atendido: true,
        respuesta_admin: respuesta,
        fecha_respuesta: new Date(),
      },
    });
  }

  async getSuggestionTypes() {
    return this.prisma.cat_Tipos_Sugerencia.findMany({
      where: { activo: true },
    });
  }

  async getUnreadSuggestions() {
    return this.prisma.sugerencia.findMany({
      where: { leido: false },
      include: {
        tipo: true,
        usuario: true,
      },
      orderBy: { fecha_envio: 'desc' },
    });
  }

  async getUnattendedSuggestions() {
    return this.prisma.sugerencia.findMany({
      where: { atendido: false },
      include: {
        tipo: true,
        usuario: true,
      },
      orderBy: { fecha_envio: 'desc' },
    });
  }
}
