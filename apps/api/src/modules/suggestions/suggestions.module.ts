import { Module } from "@nestjs/common";
import { SuggestionsService } from "./suggestions.service.js";
import { SuggestionsController } from "./suggestions.controller.js";
import { AuthModule } from "../auth/auth.module.js";

@Module({
  imports: [AuthModule],
  controllers: [SuggestionsController],
  providers: [SuggestionsService],
  exports: [SuggestionsService],
})
export class SuggestionsModule {}
