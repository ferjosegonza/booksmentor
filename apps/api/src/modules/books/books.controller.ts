import { Controller, Get, Post, Put, Delete, Body, Param, Query, UseGuards, Request } from '@nestjs/common';
import { BooksService } from './books.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@Controller('books')
@UseGuards(JwtAuthGuard)
export class BooksController {
  constructor(private booksService: BooksService) {}

  @Get('search')
  searchBooks(@Query('q') query: string) {
    return this.booksService.searchBooks(query);
  }

  @Post('create-from-google')
  createBookFromGoogle(@Body() body: { googleBooksId: string }) {
    return this.booksService.createBookFromGoogle(body.googleBooksId);
  }

  @Get(':id')
  getBookById(@Param('id') id: string) {
    return this.booksService.getBookById(parseInt(id));
  }

  @Post(':id/tags')
  addTagToBook(@Param('id') id: string, @Body() body: { tagId: number }) {
    return this.booksService.addTagToBook(parseInt(id), body.tagId);
  }

  @Delete(':id/tags/:tagId')
  removeTagFromBook(@Param('id') id: string, @Param('tagId') tagId: string) {
    return this.booksService.removeTagFromBook(parseInt(id), parseInt(tagId));
  }
}
