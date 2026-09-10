import { Injectable, OnModuleInit, OnModuleDestroy } from "@nestjs/common";
import { PrismaClient } from "../../generated/prisma/client.js";
import { PrismaMariaDb } from "@prisma/adapter-mariadb";

@Injectable()
export class PrismaService
  extends PrismaClient
  implements OnModuleInit, OnModuleDestroy
{
  constructor() {
    const adapter = new PrismaMariaDb({
      host: "localhost",
      port: 3306,
      connectionLimit: 5,
    });
    super({ adapter });
  }

  async onModuleInit() {
    await this.$connect();
  }

  async onModuleDestroy() {
    await this.$disconnect();
  }

  // async cleanDatabase() {
  //   if (process.env.NODE_ENV === 'production') return;

  //   const models = Reflect.ownKeys(this).filter((key) => key !== '_');

  //   return Promise.all(
  //     models.map((modelKey) => this[modelKey].deleteMany()),
  //   );
  // }
}
