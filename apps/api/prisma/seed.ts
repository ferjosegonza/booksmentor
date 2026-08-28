import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Starting database seed...');

  // Catálogo de Planes
  console.log('📦 Seeding Cat_Planes...');
  await prisma.cat_Planes.createMany({
    data: [
      {
        nombre: 'Gratuito',
        max_libros: 1,
        max_idiomas: 1,
        permite_audio: false,
        precio_mensual: 0.00,
        orden: 1,
      },
      {
        nombre: 'Básico',
        max_libros: 5,
        max_idiomas: 2,
        permite_audio: false,
        precio_mensual: 3.00,
        orden: 2,
      },
      {
        nombre: 'Pro',
        max_libros: 30,
        max_idiomas: 3,
        permite_audio: true,
        precio_mensual: 7.00,
        orden: 3,
      },
      {
        nombre: 'Premium',
        max_libros: 999,
        max_idiomas: 5,
        permite_audio: true,
        precio_mensual: 12.00,
        orden: 4,
      },
    ],
    skipDuplicates: true,
  });

  // Catálogo de Idiomas
  console.log('🌍 Seeding Cat_Idiomas...');
  await prisma.cat_Idiomas.createMany({
    data: [
      { nombre: 'Español', codigo: 'es' },
      { nombre: 'Inglés', codigo: 'en' },
      { nombre: 'Portugués', codigo: 'pt' },
      { nombre: 'Italiano', codigo: 'it' },
      { nombre: 'Francés', codigo: 'fr' },
      { nombre: 'Alemán', codigo: 'de' },
      { nombre: 'Chino (Simplificado)', codigo: 'zh' },
      { nombre: 'Chino (Tradicional)', codigo: 'zh-TW' },
    ],
    skipDuplicates: true,
  });

  // Catálogo de Frecuencias
  console.log('⏰ Seeding Cat_Frecuencias...');
  await prisma.cat_Frecuencias.createMany({
    data: [
      { nombre: 'Diaria', dias_entre_envios: 1, orden: 1 },
      { nombre: 'Solo laborables', dias_entre_envios: 1, orden: 2 },
      { nombre: 'Semanal', dias_entre_envios: 7, orden: 3 },
      { nombre: 'Mensual', dias_entre_envios: 30, orden: 4 },
    ],
    skipDuplicates: true,
  });

  // Catálogo de Estados de Suscripción
  console.log('📋 Seeding Cat_Estados_Suscripcion...');
  await prisma.cat_Estados_Suscripcion.createMany({
    data: [
      { nombre: 'Activo', slug: 'activo', descripcion: 'Suscripción activa y recibiendo envíos' },
      { nombre: 'Completado', slug: 'completado', descripcion: 'Todas las enseñanzas han sido enviadas' },
      { nombre: 'Pausado', slug: 'pausado', descripcion: 'Suscripción pausada por el usuario' },
    ],
    skipDuplicates: true,
  });

  // Catálogo de Estados de Envío
  console.log('📤 Seeding Cat_Estados_Envio...');
  await prisma.cat_Estados_Envio.createMany({
    data: [
      { nombre: 'Pendiente', slug: 'pendiente', descripcion: 'Envío programado pero no enviado' },
      { nombre: 'Entregado', slug: 'entregado', descripcion: 'Envío entregado exitosamente' },
      { nombre: 'Rebotado', slug: 'rebotado', descripcion: 'Email rebotado por el servidor' },
      { nombre: 'Abierto', slug: 'abierto', descripcion: 'Email abierto por el usuario' },
      { nombre: 'Fallido', slug: 'fallido', descripcion: 'Envío falló por error del sistema' },
    ],
    skipDuplicates: true,
  });

  // Catálogo de Tags
  console.log('🏷️  Seeding Cat_Tags...');
  await prisma.cat_Tags.createMany({
    data: [
      { nombre: 'Productividad', slug: 'productividad', icono: '⚡' },
      { nombre: 'Hábitos', slug: 'habitos', icono: '🔄' },
      { nombre: 'Liderazgo', slug: 'liderazgo', icono: '👥' },
      { nombre: 'Finanzas', slug: 'finanzas', icono: '💰' },
      { nombre: 'Psicología', slug: 'psicologia', icono: '🧠' },
      { nombre: 'Filosofía', slug: 'filosofia', icono: '📜' },
      { nombre: 'Creatividad', slug: 'creatividad', icono: '🎨' },
      { nombre: 'Educación', slug: 'educacion', icono: '📚' },
    ],
    skipDuplicates: true,
  });

  // Catálogo de Tipos de Sugerencia
  console.log('💬 Seeding Cat_Tipos_Sugerencia...');
  await prisma.cat_Tipos_Sugerencia.createMany({
    data: [
      { nombre: 'Sugerencia de libro', slug: 'sugerencia_libro', descripcion: 'Usuario sugiere agregar un libro' },
      { nombre: 'Reporte de error', slug: 'reporte_error', descripcion: 'Usuario reporta un error en el contenido' },
      { nombre: 'Mejora de funcionalidad', slug: 'mejora_funcionalidad', descripcion: 'Usuario sugiere una mejora' },
      { nombre: 'Otro', slug: 'otro', descripcion: 'Otro tipo de sugerencia' },
    ],
    skipDuplicates: true,
  });

  // Catálogo de IA (proveedores rotativos)
  console.log('🤖 Seeding AiCatalog...');
  await prisma.aiCatalog.createMany({
    data: [
      {
        proveedor: 'gemini',
        tipo_uso: 'generacion_ensenanzas',
        modelo: 'gemini-pro',
        limite_tokens: 1500000,
        limite_peticiones_diarias: 15,
        prioridad: 1,
        estado: 'activo',
      },
      {
        proveedor: 'groq',
        tipo_uso: 'generacion_ensenanzas',
        modelo: 'llama2-70b-4096',
        limite_tokens: 100000,
        limite_peticiones_diarias: 30,
        prioridad: 2,
        estado: 'activo',
      },
      {
        proveedor: 'huggingface',
        tipo_uso: 'generacion_ensenanzas',
        modelo: 'meta-llama/Llama-2-70b-chat-hf',
        limite_tokens: 50000,
        limite_peticiones_diarias: 100,
        prioridad: 3,
        estado: 'activo',
      },
      {
        proveedor: 'gemini',
        tipo_uso: 'traduccion',
        modelo: 'gemini-pro',
        limite_tokens: 1500000,
        limite_peticiones_diarias: 15,
        prioridad: 1,
        estado: 'activo',
      },
      {
        proveedor: 'groq',
        tipo_uso: 'traduccion',
        modelo: 'llama2-70b-4096',
        limite_tokens: 100000,
        limite_peticiones_diarias: 30,
        prioridad: 2,
        estado: 'activo',
      },
      {
        proveedor: 'huggingface',
        tipo_uso: 'traduccion',
        modelo: 'meta-llama/Llama-2-70b-chat-hf',
        limite_tokens: 50000,
        limite_peticiones_diarias: 100,
        prioridad: 3,
        estado: 'activo',
      },
    ],
    skipDuplicates: true,
  });

  // Catálogo de Email (proveedores rotativos)
  console.log('📧 Seeding EmailCatalog...');
  await prisma.emailCatalog.createMany({
    data: [
      {
        proveedor: 'resend',
        limite_mensual: 3000,
        limite_diario: null,
        prioridad: 1,
        estado: 'activo',
      },
      {
        proveedor: 'brevo',
        limite_mensual: null,
        limite_diario: 300,
        prioridad: 2,
        estado: 'activo',
      },
      {
        proveedor: 'ses',
        limite_mensual: 62000,
        limite_diario: null,
        prioridad: 3,
        estado: 'activo',
      },
    ],
    skipDuplicates: true,
  });

  // Catálogo de Payment Providers
  console.log('💳 Seeding PaymentProviders...');
  await prisma.paymentProviders.createMany({
    data: [
      {
        proveedor: 'mercadopago',
        activo: true,
        prioridad: 1,
        region: 'AR',
      },
      {
        proveedor: 'paypal',
        activo: true,
        prioridad: 2,
        region: 'GLOBAL',
      },
      {
        proveedor: 'stripe',
        activo: false,
        prioridad: 3,
        region: 'GLOBAL',
      },
    ],
    skipDuplicates: true,
  });

  console.log('✅ Database seed completed successfully!');
}

main()
  .catch((e) => {
    console.error('❌ Error during seed:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
