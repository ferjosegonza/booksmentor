import { Module } from '@nestjs/common';
import { AdminService } from './admin.service';
import { AdminController } from './admin.controller';
import { BooksModule } from '../books/books.module';
import { AiModule } from '../ai/ai.module';
import { SuggestionsModule } from '../suggestions/suggestions.module';

@Module({
  imports: [BooksModule, AiModule, SuggestionsModule],
  controllers: [AdminController],
  providers: [AdminService],
})
export class AdminModule {}
