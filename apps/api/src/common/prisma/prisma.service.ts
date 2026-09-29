import { Injectable, OnModuleInit, OnModuleDestroy } from "@nestjs/common";
import { PrismaClient } from "../../generated/prisma/client.js";
import { PrismaMariaDb } from "@prisma/adapter-mariadb";
import { ModelName } from "../../generated/prisma/internal/prismaNamespace.js";

@Injectable()
export class PrismaService
  extends PrismaClient
  implements OnModuleInit, OnModuleDestroy
{
  constructor() {
    const dbUrl = new URL(process.env.DATABASE_URL as string);

    const adapterOptions = {
      host: dbUrl.hostname,
      port: parseInt(dbUrl.port),
      user: dbUrl.username,
      password: "root",
      database: dbUrl.pathname.slice(1),
      allowPublicKeyRetrieval: false,
    };

    if (process.env.NODE_ENV === 'development') {
      adapterOptions.allowPublicKeyRetrieval = true;
    }

    const adapter = new PrismaMariaDb(adapterOptions);
    super({ adapter });
  }

  async onModuleInit() {
    await this.$connect();
  }

  async onModuleDestroy() {
    await this.$disconnect();
  }

  // no estoy muy seguro de como se usaria esto, pero habria que tener en cuenta:
  // - no se pueden primero truncar tablas de las cuales dependen otras, habria que inspeccionar las dependencias
  // - no estoy seguro que se pueda hacer en paralelo (Promise.all) mediante 1 misma conexion/cliente
  // - deberia correr todo en una transaccion imagino
  // - por ahi seria mejor dejarselo a prisma: npx prisma migrate reset

  // async cleanDatabase() {
  //   if (process.env.NODE_ENV === "production") return;

  //   const models = Reflect.ownKeys(ModelName).filter((key) => key !== "_");

  //   return Promise.all(models.map(modelKey => this[modelKey].deleteMany()));
  // }
}
