import { Controller, Get, Post, Put, Body, Param, UseGuards, Request } from '@nestjs/common';
import { AdminService } from './admin.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@Controller('admin')
@UseGuards(JwtAuthGuard)
export class AdminController {
  constructor(private adminService: AdminService) {}

  // Teaching Review
  @Get('teachings/pending')
  getPendingTeachings() {
    return this.adminService.getPendingTeachings();
  }

  @Put('teachings/:id/approve')
  approveTeaching(@Param('id') id: string, @Request() req) {
    return this.adminService.approveTeaching(parseInt(id), req.user.id);
  }

  @Put('teachings/:id/reject')
  rejectTeaching(@Param('id') id: string, @Request() req, @Body() body?: { reason?: string }) {
    return this.adminService.rejectTeaching(parseInt(id), req.user.id, body?.reason);
  }

  @Post('teachings/:id/regenerate')
  regenerateTeaching(@Param('id') id: string, @Request() req) {
    return this.adminService.regenerateTeaching(parseInt(id), req.user.id);
  }

  @Put('teachings/:id/edit')
  editTeaching(@Param('id') id: string, @Request() req, @Body() body: { newText: string }) {
    return this.adminService.editTeaching(parseInt(id), req.user.id, body.newText);
  }

  // Catalog Management
  @Get('catalogs')
  getAllCatalogs() {
    return this.adminService.getAllCatalogs();
  }

  @Put('catalogs/:catalog/:id')
  updateCatalogItem(@Param('catalog') catalog: string, @Param('id') id: string, @Body() body: any) {
    return this.adminService.updateCatalogItem(catalog, parseInt(id), body);
  }

  // Provider Management
  @Get('providers/status')
  getProviderStatus() {
    return this.adminService.getProviderStatus();
  }

  @Put('providers/ai/:id')
  updateAiProvider(@Param('id') id: string, @Body() body: any) {
    return this.adminService.updateAiProvider(parseInt(id), body);
  }

  @Put('providers/email/:id')
  updateEmailProvider(@Param('id') id: string, @Body() body: any) {
    return this.adminService.updateEmailProvider(parseInt(id), body);
  }

  @Put('providers/payment/:id')
  updatePaymentProvider(@Param('id') id: string, @Body() body: any) {
    return this.adminService.updatePaymentProvider(parseInt(id), body);
  }

  // Dashboard
  @Get('dashboard')
  getDashboardStats() {
    return this.adminService.getDashboardStats();
  }

  // User Management
  @Get('users')
  getAllUsers() {
    return this.adminService.getAllUsers();
  }

  @Put('users/:id/role')
  updateUserRole(@Param('id') id: string, @Body() body: { role: string }) {
    return this.adminService.updateUserRole(parseInt(id), body.role);
  }

  @Put('users/:id/plan')
  updateUserPlan(@Param('id') id: string, @Body() body: { planId: number }) {
    return this.adminService.updateUserPlan(parseInt(id), body.planId);
  }

  @Put('users/:id/deactivate')
  deactivateUser(@Param('id') id: string) {
    return this.adminService.deactivateUser(parseInt(id));
  }
}
