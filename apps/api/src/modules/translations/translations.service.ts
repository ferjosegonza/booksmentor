import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../common/prisma/prisma.service';
import { AiService } from '../ai/ai.service';

@Injectable()
export class TranslationsService {
  constructor(
    private prisma: PrismaService,
    private aiService: AiService,
  ) {}

  async getOrGenerateTranslation(enseñanzaId: number, idiomaId: number): Promise<string> {
    // Check if translation already exists
    const existingTranslation = await this.prisma.traduccion.findUnique({
      where: {
        enseñanza_id_idioma_id: {
          enseñanza_id: enseñanzaId,
          idioma_id: idiomaId,
        },
      },
    });

    if (existingTranslation) {
      // Update usage stats
      await this.prisma.traduccion.update({
        where: { id: existingTranslation.id },
        data: {
          veces_usado: { increment: 1 },
          ultimo_uso: new Date(),
        },
      });

      return existingTranslation.texto_traducido;
    }

    // Get the original teaching
    const enseñanza = await this.prisma.enseñanza.findUnique({
      where: { id: enseñanzaId },
      include: { libro: true },
    });

    if (!enseñanza) {
      throw new NotFoundException('Teaching not found');
    }

    // Get target language
    const idioma = await this.prisma.cat_Idiomas.findUnique({
      where: { id: idiomaId },
    });

    if (!idioma) {
      throw new NotFoundException('Language not found');
    }

    // Generate translation using AI
    const translatedText = await this.aiService.translate(
      enseñanza.texto_original,
      idioma.nombre,
    );

    // Store translation
    const translation = await this.prisma.traduccion.create({
      data: {
        enseñanza_id: enseñanzaId,
        idioma_id: idiomaId,
        texto_traducido: translatedText,
        veces_usado: 1,
        ultimo_uso: new Date(),
      },
    });

    return translation.texto_traducido;
  }

  async getTranslationById(id: number) {
    return this.prisma.traduccion.findUnique({
      where: { id },
      include: {
        enseñanza: {
          include: { libro: true },
        },
        idioma: true,
      },
    });
  }

  async getTranslationsByTeaching(enseñanzaId: number) {
    return this.prisma.traduccion.findMany({
      where: { enseñanza_id: enseñanzaId },
      include: { idioma: true },
    });
  }

  async getTranslationsByLanguage(idiomaId: number) {
    return this.prisma.traduccion.findMany({
      where: { idioma_id: idiomaId },
      include: {
        enseñanza: {
          include: { libro: true },
        },
      },
    });
  }

  async regenerateTranslation(translationId: number) {
    const translation = await this.prisma.traduccion.findUnique({
      where: { id: translationId },
      include: {
        enseñanza: true,
        idioma: true,
      },
    });

    if (!translation) {
      throw new NotFoundException('Translation not found');
    }

    // Generate new translation
    const newTranslatedText = await this.aiService.translate(
      translation.enseñanza.texto_original,
      translation.idioma.nombre,
    );

    // Update translation
    return this.prisma.traduccion.update({
      where: { id: translationId },
      data: {
        texto_traducido: newTranslatedText,
        version: { increment: 1 },
        veces_usado: 0,
        ultimo_uso: new Date(),
      },
    });
  }

  async getTranslationStats() {
    const totalTranslations = await this.prisma.traduccion.count();
    const totalUsage = await this.prisma.traduccion.aggregate({
      _sum: { veces_usado: true },
    });

    const mostUsedTranslations = await this.prisma.traduccion.findMany({
      orderBy: { veces_usado: 'desc' },
      take: 10,
      include: {
        enseñanza: {
          include: { libro: true },
        },
        idioma: true,
      },
    });

    return {
      totalTranslations,
      totalUsage: totalUsage._sum.veces_usado || 0,
      mostUsedTranslations,
    };
  }
}
