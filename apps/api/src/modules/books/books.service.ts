import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../common/prisma/prisma.service';
import axios from 'axios';

interface GoogleBook {
  id: string;
  volumeInfo: {
    title: string;
    authors?: string[];
    publishedDate?: string;
    imageLinks?: {
      thumbnail?: string;
    };
    industryIdentifiers?: Array<{
      type: string;
      identifier: string;
    }>;
  };
}

@Injectable()
export class BooksService {
  constructor(private prisma: PrismaService) {}

  async searchBooks(query: string) {
    // Search in local database first
    const localBooks = await this.prisma.libro.findMany({
      where: {
        OR: [
          { titulo: { contains: query, mode: 'insensitive' } },
          { autor: { contains: query, mode: 'insensitive' } },
        ],
        activo: true,
      },
      take: 10,
      include: { idioma_original: true },
    });

    // Search in Google Books API
    let googleBooks: GoogleBook[] = [];
    try {
      const response = await axios.get(
        `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&maxResults=10`,
      );
      googleBooks = response.data.items || [];
    } catch (error) {
      console.error('Error searching Google Books:', error);
    }

    // Combine and deduplicate results
    const combinedResults = [
      ...localBooks.map((book) => ({
        id: book.id,
        type: 'local',
        title: book.titulo,
        author: book.autor,
        year: book.año_publicacion,
        cover: book.portada_url,
        isbn: book.isbn,
        googleBooksId: book.google_books_id,
        language: book.idioma_original.codigo,
      })),
      ...googleBooks.map((book) => ({
        id: book.id,
        type: 'google',
        title: book.volumeInfo.title,
        author: book.volumeInfo.authors?.[0] || 'Unknown',
        year: book.volumeInfo.publishedDate?.substring(0, 4) || null,
        cover: book.volumeInfo.imageLinks?.thumbnail || null,
        isbn: book.volumeInfo.industryIdentifiers?.find((id) => id.type === 'ISBN_13')?.identifier || null,
        googleBooksId: book.id,
        language: null,
      })),
    ];

    return combinedResults;
  }

  async createBookFromGoogle(googleBooksId: string) {
    // Check if book already exists
    const existingBook = await this.prisma.libro.findUnique({
      where: { google_books_id: googleBooksId },
    });

    if (existingBook) {
      return existingBook;
    }

    // Fetch from Google Books API
    const response = await axios.get(
      `https://www.googleapis.com/books/v1/volumes/${googleBooksId}`,
    );
    const googleBook = response.data;

    // Find or create language
    const languageCode = googleBook.volumeInfo.language || 'en';
    let language = await this.prisma.cat_Idiomas.findUnique({
      where: { codigo: languageCode },
    });

    if (!language) {
      language = await this.prisma.cat_Idiomas.create({
        data: {
          nombre: languageCode.toUpperCase(),
          codigo: languageCode,
        },
      });
    }

    // Extract ISBN
    const isbn = googleBook.volumeInfo.industryIdentifiers?.find(
      (id: any) => id.type === 'ISBN_13' || id.type === 'ISBN_10',
    )?.identifier;

    // Create book
    const book = await this.prisma.libro.create({
      data: {
        titulo: googleBook.volumeInfo.title,
        autor: googleBook.volumeInfo.authors?.[0] || 'Unknown',
        isbn: isbn || null,
        idioma_original_id: language.id,
        año_publicacion: googleBook.volumeInfo.publishedDate?.substring(0, 4)
          ? parseInt(googleBook.volumeInfo.publishedDate.substring(0, 4))
          : null,
        portada_url: googleBook.volumeInfo.imageLinks?.thumbnail || null,
        google_books_id: googleBooksId,
        estado: 'borrador_ia_pendiente_revision',
      },
      include: { idioma_original: true },
    });

    return book;
  }

  async getBookById(id: number) {
    const book = await this.prisma.libro.findUnique({
      where: { id },
      include: {
        idioma_original: true,
        libro_tags: {
          include: { tag: true },
        },
        enseñanzas: {
          where: { estado: 'aprobado' },
          orderBy: { orden: 'asc' },
        },
      },
    });

    if (!book) {
      throw new NotFoundException('Book not found');
    }

    return book;
  }

  async addTagToBook(bookId: number, tagId: number) {
    return this.prisma.libro_Tags.create({
      data: {
        libro_id: bookId,
        tag_id: tagId,
      },
      include: { tag: true },
    });
  }

  async removeTagFromBook(bookId: number, tagId: number) {
    await this.prisma.libro_Tags.deleteMany({
      where: {
        libro_id: bookId,
        tag_id: tagId,
      },
    });
  }
}
