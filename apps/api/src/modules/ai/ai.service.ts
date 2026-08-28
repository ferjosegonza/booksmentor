import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../common/prisma/prisma.service';
import { GoogleGenerativeAI } from '@google/generative-ai';
import Groq from 'groq-sdk';
import { HfInference } from '@huggingface/inference';

interface AiProvider {
  generateTeaching(bookTitle: string, bookAuthor: string, bookTheme?: string): Promise<string>;
  translate(text: string, targetLanguage: string): Promise<string>;
  getName(): string;
}

class GeminiProvider implements AiProvider {
  private logger = new Logger(GeminiProvider.name);
  private genAI: GoogleGenerativeAI;

  constructor(private apiKey: string) {
    this.genAI = new GoogleGenerativeAI(apiKey);
  }

  getName(): string {
    return 'gemini';
  }

  async generateTeaching(bookTitle: string, bookAuthor: string, bookTheme?: string): Promise<string> {
    try {
      const model = this.genAI.getGenerativeModel({ model: 'gemini-pro' });
      
      const prompt = `Genera una ensenanza breve (2-3 parrafos) basada en el libro "${bookTitle}" de ${bookAuthor}. 
      ${bookTheme ? `Enfocate en el tema: ${bookTheme}.` : ''}
      
      IMPORTANTE:
      - NO cites ni reproduzcas texto original del libro
      - Resume las ideas principales en tus propias palabras
      - Se conciso y practico
      - Enfocate en conceptos aplicables
      - NO incluyas frases como "En el libro se dice que..."`;

      const result = await model.generateContent(prompt);
      const response = await result.response;
      return response.text();
    } catch (error) {
      this.logger.error(`Error generating teaching with Gemini: ${error.message}`);
      throw error;
    }
  }

  async translate(text: string, targetLanguage: string): Promise<string> {
    try {
      const model = this.genAI.getGenerativeModel({ model: 'gemini-pro' });
      
      const prompt = `Traduce el siguiente texto al ${targetLanguage}. Mantén el significado original y el tono:\n\n${text}`;

      const result = await model.generateContent(prompt);
      const response = await result.response;
      return response.text();
    } catch (error) {
      this.logger.error(`Error translating with Gemini: ${error.message}`);
      throw error;
    }
  }
}

class GroqProvider implements AiProvider {
  private logger = new Logger(GroqProvider.name);
  private groq: Groq;

  constructor(private apiKey: string) {
    this.groq = new Groq({ apiKey });
  }

  getName(): string {
    return 'groq';
  }

  async generateTeaching(bookTitle: string, bookAuthor: string, bookTheme?: string): Promise<string> {
    try {
      const prompt = `Genera una ensenanza breve (2-3 párrafos) basada en el libro "${bookTitle}" de ${bookAuthor}. 
      ${bookTheme ? `Enfócate en el tema: ${bookTheme}.` : ''}
      
      IMPORTANTE:
      - NO cites ni reproduzcas texto original del libro
      - Resume las ideas principales en tus propias palabras
      - Sé conciso y práctico
      - Enfócate en conceptos aplicables
      - NO incluyas frases como "En el libro se dice que..."`;

      const response = await this.groq.chat.completions.create({
        messages: [{ role: 'user', content: prompt }],
        model: 'llama2-70b-4096',
      });

      return response.choices[0]?.message?.content || '';
    } catch (error) {
      this.logger.error(`Error generating teaching with Groq: ${error.message}`);
      throw error;
    }
  }

  async translate(text: string, targetLanguage: string): Promise<string> {
    try {
      const prompt = `Traduce el siguiente texto al ${targetLanguage}. Mantén el significado original y el tono:\n\n${text}`;

      const response = await this.groq.chat.completions.create({
        messages: [{ role: 'user', content: prompt }],
        model: 'llama2-70b-4096',
      });

      return response.choices[0]?.message?.content || '';
    } catch (error) {
      this.logger.error(`Error translating with Groq: ${error.message}`);
      throw error;
    }
  }
}

class HuggingFaceProvider implements AiProvider {
  private logger = new Logger(HuggingFaceProvider.name);
  private hf: HfInference;

  constructor(private apiKey: string) {
    this.hf = new HfInference(apiKey);
  }

  getName(): string {
    return 'huggingface';
  }

  async generateTeaching(bookTitle: string, bookAuthor: string, bookTheme?: string): Promise<string> {
    try {
      const prompt = `Genera una ensenanza breve (2-3 párrafos) basada en el libro "${bookTitle}" de ${bookAuthor}. 
      ${bookTheme ? `Enfócate en el tema: ${bookTheme}.` : ''}
      
      IMPORTANTE:
      - NO cites ni reproduzcas texto original del libro
      - Resume las ideas principales en tus propias palabras
      - Sé conciso y práctico
      - Enfócate en conceptos aplicables
      - NO incluyas frases como "En el libro se dice que..."`;

      const response = await this.hf.textGeneration({
        model: 'meta-llama/Llama-2-70b-chat-hf',
        inputs: prompt,
        parameters: {
          max_new_tokens: 500,
          temperature: 0.7,
        },
      });

      return response.generated_text || '';
    } catch (error) {
      this.logger.error(`Error generating teaching with HuggingFace: ${error.message}`);
      throw error;
    }
  }

  async translate(text: string, targetLanguage: string): Promise<string> {
    try {
      const prompt = `Traduce el siguiente texto al ${targetLanguage}. Mantén el significado original y el tono:\n\n${text}`;

      const response = await this.hf.textGeneration({
        model: 'meta-llama/Llama-2-70b-chat-hf',
        inputs: prompt,
        parameters: {
          max_new_tokens: 500,
          temperature: 0.3,
        },
      });

      return response.generated_text || '';
    } catch (error) {
      this.logger.error(`Error translating with HuggingFace: ${error.message}`);
      throw error;
    }
  }
}

@Injectable()
export class AiService {
  private logger = new Logger(AiService.name);
  private providers: Map<string, AiProvider> = new Map();

  constructor(private prisma: PrismaService) {
    this.initializeProviders();
  }

  private initializeProviders() {
    // Initialize Gemini
    const geminiKey = process.env.GEMINI_API_KEY;
    if (geminiKey) {
      this.providers.set('gemini', new GeminiProvider(geminiKey));
    }

    // Initialize Groq
    const groqKey = process.env.GROQ_API_KEY;
    if (groqKey) {
      this.providers.set('groq', new GroqProvider(groqKey));
    }

    // Initialize HuggingFace
    const hfKey = process.env.HUGGINGFACE_API_KEY;
    if (hfKey) {
      this.providers.set('huggingface', new HuggingFaceProvider(hfKey));
    }

    this.logger.log(`Initialized ${this.providers.size} AI providers`);
  }

  private async getAvailableProvider(tipoUso: string): Promise<AiProvider> {
    const catalog = await this.prisma.aiCatalog.findMany({
      where: {
        tipo_uso: tipoUso,
        estado: 'activo',
        activo: true,
      },
      orderBy: { prioridad: 'asc' },
    });

    for (const entry of catalog) {
      const provider = this.providers.get(entry.proveedor);
      if (provider) {
        // Check if provider is exhausted
        if (entry.consumo_tokens >= entry.limite_tokens) {
          await this.prisma.aiCatalog.update({
            where: { id: entry.id },
            data: { estado: 'agotado' },
          });
          continue;
        }
        return provider;
      }
    }

    throw new Error('No available AI providers');
  }

  private async updateProviderUsage(providerName: string, tipoUso: string, tokensUsed: number) {
    const catalog = await this.prisma.aiCatalog.findFirst({
      where: {
        proveedor: providerName,
        tipo_uso: tipoUso,
      },
    });

    if (catalog) {
      await this.prisma.aiCatalog.update({
        where: { id: catalog.id },
        data: {
          consumo_tokens: { increment: tokensUsed },
          consumo_peticiones_diarias: { increment: 1 },
        },
      });
    }
  }

  async generateTeaching(bookTitle: string, bookAuthor: string, bookTheme?: string): Promise<string> {
    try {
      const provider = await this.getAvailableProvider('generacion_ensenanzas');
      const result = await provider.generateTeaching(bookTitle, bookAuthor, bookTheme);
      
      // Estimate tokens (rough approximation: 1 token ≈ 4 characters)
      const tokensUsed = Math.ceil(result.length / 4);
      await this.updateProviderUsage(provider.getName(), 'generacion_ensenanzas', tokensUsed);

      // Log usage
      await this.prisma.auditoria_IA.create({
        data: {
          ai_catalog_id: (await this.prisma.aiCatalog.findFirst({
            where: { proveedor: provider.getName(), tipo_uso: 'generacion_ensenanzas' },
          }))?.id || 0,
          proveedor: provider.getName(),
          modelo: 'default',
          cuota_usada: tokensUsed,
          tokens_usados: tokensUsed,
          solicitud: { bookTitle, bookAuthor, bookTheme },
          resultado: { teaching: result },
          exitoso: true,
        },
      });

      return result;
    } catch (error) {
      this.logger.error(`Error generating teaching: ${error.message}`);
      
      // Log error
      await this.prisma.auditoria_IA.create({
        data: {
          ai_catalog_id: 0,
          proveedor: 'unknown',
          modelo: 'default',
          cuota_usada: 0,
          solicitud: { bookTitle, bookAuthor, bookTheme },
          error: error.message,
          exitoso: false,
        },
      });

      throw error;
    }
  }

  async translate(text: string, targetLanguage: string): Promise<string> {
    try {
      const provider = await this.getAvailableProvider('traduccion');
      const result = await provider.translate(text, targetLanguage);
      
      // Estimate tokens
      const tokensUsed = Math.ceil((text.length + result.length) / 4);
      await this.updateProviderUsage(provider.getName(), 'traduccion', tokensUsed);

      // Log usage
      await this.prisma.auditoria_IA.create({
        data: {
          ai_catalog_id: (await this.prisma.aiCatalog.findFirst({
            where: { proveedor: provider.getName(), tipo_uso: 'traduccion' },
          }))?.id || 0,
          proveedor: provider.getName(),
          modelo: 'default',
          cuota_usada: tokensUsed,
          tokens_usados: tokensUsed,
          solicitud: { text, targetLanguage },
          resultado: { translation: result },
          exitoso: true,
        },
      });

      return result;
    } catch (error) {
      this.logger.error(`Error translating: ${error.message}`);
      
      // Log error
      await this.prisma.auditoria_IA.create({
        data: {
          ai_catalog_id: 0,
          proveedor: 'unknown',
          modelo: 'default',
          cuota_usada: 0,
          solicitud: { text, targetLanguage },
          error: error.message,
          exitoso: false,
        },
      });

      throw error;
    }
  }

  async regenerateTeaching(bookId: number, teachingOrder: number): Promise<string> {
    const book = await this.prisma.libro.findUnique({
      where: { id: bookId },
    });

    if (!book) {
      throw new Error('Book not found');
    }

    return this.generateTeaching(book.titulo, book.autor);
  }

  async checkAndRenewProviders() {
    const now = new Date();
    const exhaustedProviders = await this.prisma.aiCatalog.findMany({
      where: {
        estado: 'agotado',
        fecha_renovacion: { lte: now },
      },
    });

    for (const provider of exhaustedProviders) {
      await this.prisma.aiCatalog.update({
        where: { id: provider.id },
        data: {
          estado: 'activo',
          consumo_tokens: 0,
          consumo_peticiones_diarias: 0,
        },
      });
      this.logger.log(`Renewed provider: ${provider.proveedor}`);
    }
  }
}
