import { Module } from "@nestjs/common";
import { AdminService } from "./admin.service.js";
import { AdminController } from "./admin.controller.js";
import { BooksModule } from "../books/books.module.js";
import { AiModule } from "../ai/ai.module.js";
import { SuggestionsModule } from "../suggestions/suggestions.module.js";

@Module({
  imports: [BooksModule, AiModule, SuggestionsModule],
  controllers: [AdminController],
  providers: [AdminService],
})
export class AdminModule {}
