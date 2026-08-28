import { Injectable, UnauthorizedException, ConflictException } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { ConfigService } from '@nestjs/config';
import { UsersService } from '../users/users.service';
import { PrismaService } from '../../common/prisma/prisma.service';
import * as bcrypt from 'bcrypt';

@Injectable()
export class AuthService {
  constructor(
    private usersService: UsersService,
    private jwtService: JwtService,
    private configService: ConfigService,
    private prisma: PrismaService,
  ) {}

  async validateUser(email: string, password: string): Promise<any> {
    const user = await this.prisma.usuario.findUnique({
      where: { email },
      include: { plan: true, frecuencia: true },
    });

    if (!user || !user.password) {
      return null;
    }

    const isPasswordValid = await bcrypt.compare(password, user.password);
    if (!isPasswordValid) {
      return null;
    }

    const { password: _, ...result } = user;
    return result;
  }

  async login(user: any) {
    const payload = { email: user.email, sub: user.id, role: user.role };
    return {
      access_token: this.jwtService.sign(payload),
      user: {
        id: user.id,
        email: user.email,
        nombre: user.nombre,
        role: user.role,
      },
    };
  }

  async register(email: string, password: string, nombre: string, acceptPolicy: boolean, ipAddress?: string, userAgent?: string) {
    if (!acceptPolicy) {
      throw new ConflictException('Debe aceptar la Política de Uso para registrarse');
    }

    const existingUser = await this.prisma.usuario.findUnique({
      where: { email },
    });

    if (existingUser) {
      throw new ConflictException('El email ya está registrado');
    }

    const hashedPassword = await bcrypt.hash(password, 10);

    // Get default plan and frequency
    const defaultPlan = await this.prisma.cat_Planes.findFirst({
      where: { nombre: 'Gratuito' },
    });

    const defaultFrequency = await this.prisma.cat_Frecuencias.findFirst({
      where: { nombre: 'Semanal' },
    });

    if (!defaultPlan || !defaultFrequency) {
      throw new Error('Configuration error: default plan or frequency not found');
    }

    const user = await this.prisma.usuario.create({
      data: {
        email,
        password: hashedPassword,
        nombre,
        plan_id: defaultPlan.id,
        frecuencia_id: defaultFrequency.id,
        canal_entrega: 'email',
        zona_horaria: 'UTC',
        hora_envio: 9,
        role: 'usuario',
      },
      include: { plan: true, frecuencia: true },
    });

    // Record legal acceptance
    await this.prisma.userLegalAcceptance.create({
      data: {
        usuario_id: user.id,
        version_politica: '1.0',
        ip_address: ipAddress,
        user_agent: userAgent,
      },
    });

    const { password: _, ...result } = user;
    return result;
  }

  async googleLogin(profile: any) {
    let user = await this.prisma.usuario.findUnique({
      where: { google_id: profile.id },
      include: { plan: true, frecuencia: true },
    });

    if (!user) {
      // Check if email already exists
      const existingUser = await this.prisma.usuario.findUnique({
        where: { email: profile.email },
      });

      if (existingUser) {
        // Link Google account to existing user
        user = await this.prisma.usuario.update({
          where: { id: existingUser.id },
          data: { google_id: profile.id },
          include: { plan: true, frecuencia: true },
        });
      } else {
        // Create new user with Google
        const defaultPlan = await this.prisma.cat_Planes.findFirst({
          where: { nombre: 'Gratuito' },
        });

        const defaultFrequency = await this.prisma.cat_Frecuencias.findFirst({
          where: { nombre: 'Semanal' },
        });

        if (!defaultPlan || !defaultFrequency) {
          throw new Error('Configuration error: default plan or frequency not found');
        }

        user = await this.prisma.usuario.create({
          data: {
            email: profile.email,
            nombre: profile.name || profile.email.split('@')[0],
            google_id: profile.id,
            plan_id: defaultPlan.id,
            frecuencia_id: defaultFrequency.id,
            canal_entrega: 'email',
            zona_horaria: 'UTC',
            hora_envio: 9,
            role: 'usuario',
          },
          include: { plan: true, frecuencia: true },
        });

        // Record legal acceptance
        await this.prisma.userLegalAcceptance.create({
          data: {
            usuario_id: user.id,
            version_politica: '1.0',
          },
        });
      }
    }

    const payload = { email: user.email, sub: user.id, role: user.role };
    return {
      access_token: this.jwtService.sign(payload),
      user: {
        id: user.id,
        email: user.email,
        nombre: user.nombre,
        role: user.role,
      },
    };
  }
}
