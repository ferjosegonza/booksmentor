# BookMentor - Sistema de Envío de Enseñanzas por Suscripción

Sistema de envío de enseñanzas extraídas de libros de forma personalizada y progresiva. Los usuarios pueden suscribirse a libros y recibir enseñanzas traducidas al idioma que elijan, por email o dentro de la app.

## 🏗️ Arquitectura

BookMentor es un monorepo que contiene:

- **apps/api**: Backend NestJS con TypeScript
- **apps/web**: Frontend web Next.js con React
- **apps/mobile**: App móvil React Native con Expo
- **packages/shared**: Código compartido entre aplicaciones
- **docs**: Documentación del proyecto

## 🚀 Stack Tecnológico

### Backend
- **Framework**: NestJS (TypeScript)
- **Base de datos**: MySQL 8.0+
- **ORM**: Prisma
- **Colas**: BullMQ + Redis
- **Autenticación**: Passport + Google OAuth
- **IA**: Google Gemini, Groq, Hugging Face (catálogo rotativo)
- **Email**: Resend, Brevo, Amazon SES (catálogo rotativo)
- **Pagos**: MercadoPago, PayPal, Stripe (interfaz plug-and-play)

### Frontend Web
- **Framework**: Next.js 14 (React)
- **Estilos**: Tailwind CSS
- **Estado**: React Hooks + Context
- **Autenticación**: NextAuth.js

### Mobile
- **Framework**: React Native + Expo
- **Navegación**: Expo Router
- **Notificaciones**: Expo Notifications
- **Estado**: React Hooks + AsyncStorage

## 📋 Requisitos Previos

- Node.js >= 18.0.0
- npm >= 9.0.0
- MySQL >= 8.0
- Redis >= 6
- Git

## 🔧 Instalación

### 1. Clonar el repositorio

```bash
git clone <repository-url>
cd booksmentor
```

### 2. Instalar dependencias

```bash
npm install
```

Esto instalará las dependencias de todas las aplicaciones (monorepo con workspaces).

### 3. Configurar variables de entorno

Copia los archivos de ejemplo de entorno para cada aplicación:

```bash
# Backend API
cp apps/api/.env.example apps/api/.env

# Frontend Web
cp apps/web/.env.local.example apps/web/.env.local

# Mobile
cp apps/mobile/.env.local.example apps/mobile/.env.local
```

Edita cada archivo `.env` con tus configuraciones locales:

#### Backend API (apps/api/.env)
```env
DATABASE_URL="postgresql://user:password@localhost:5432/booksmentor?schema=public"
REDIS_HOST="localhost"
REDIS_PORT=6379
JWT_SECRET="your-secret-key"
GOOGLE_CLIENT_ID="your-google-client-id"
GOOGLE_CLIENT_SECRET="your-google-client-secret"
# ... otras variables
```

#### Frontend Web (apps/web/.env.local)
```env
NEXT_PUBLIC_API_URL="http://localhost:3001"
```

#### Mobile (apps/mobile/.env.local)
```env
EXPO_PUBLIC_API_URL="http://localhost:3001"
```

### 4. Configurar base de datos

#### Crear base de datos MySQL

```bash
# En sistemas Unix/Linux con MySQL instalado
mysql -u root -p -e "CREATE DATABASE booksmentor;"

# En Windows con XAMPP (MySQL incluido)
# Abre phpMyAdmin o usa la consola de MySQL desde XAMPP
```

#### Ejecutar migraciones de Prisma

```bash
cd apps/api
npx prisma migrate dev
```

#### Sembrar datos iniciales (seeders)

```bash
npx prisma db seed
```

Esto creará los catálogos base (planes, idiomas, frecuencias, tags, proveedores).

### 5. Iniciar Redis

```bash
# En sistemas Unix/Linux con Redis instalado
redis-server

# En Windows, puedes usar WSL o Docker
docker run -d -p 6379:6379 redis
```

## 🎯 Desarrollo

### Iniciar todas las aplicaciones

```bash
npm run dev
```

Esto iniciará:
- Backend API en http://localhost:3001
- Frontend Web en http://localhost:3000
- Mobile (requiere Expo CLI)

### Iniciar aplicaciones individualmente

#### Backend API
```bash
cd apps/api
npm run dev
```

El API estará disponible en http://localhost:3001
Documentación Swagger: http://localhost:3001/api/docs

#### Frontend Web
```bash
cd apps/web
npm run dev
```

La aplicación web estará disponible en http://localhost:3000

#### Mobile
```bash
cd apps/mobile
npm start
```

Esto abrirá Expo DevTools. Puedes escanear el QR con la app Expo Go en tu móvil.

## 🗄️ Gestión de Base de Datos

### Crear nueva migración

```bash
cd apps/api
npx prisma migrate dev --name nombre_migracion
```

### Generar cliente Prisma

```bash
cd apps/api
npx prisma generate
```

### Abrir Prisma Studio (GUI)

```bash
cd apps/api
npx prisma studio
```

### Resetear base de datos (⚠️ borra todos los datos)

```bash
cd apps/api
npx prisma migrate reset
```

## 🧪 Pruebas

### Ejecutar pruebas del backend

```bash
cd apps/api
npm run test
```

### Ejecutar pruebas con cobertura

```bash
cd apps/api
npm run test:cov
```

## 📦 Build para Producción

### Backend API

```bash
cd apps/api
npm run build
npm run start:prod
```

### Frontend Web

```bash
cd apps/web
npm run build
npm run start
```

### Mobile

```bash
cd apps/mobile
# Para iOS
eas build --platform ios

# Para Android
eas build --platform android
```

## 🔐 Seguridad

### Variables de entorno sensibles

Nunca commits archivos `.env` con datos reales. Usa los archivos `.env.example` como plantillas.

### Credenciales de proveedores

Configura las credenciales de IA, email y pagos en los archivos `.env` correspondientes:

- **IA**: GEMINI_API_KEY, GROQ_API_KEY, HUGGINGFACE_API_KEY
- **Email**: RESEND_API_KEY, BREVO_API_KEY, AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY
- **Pagos**: MERCADOPAGO_ACCESS_TOKEN, PAYPAL_CLIENT_ID, PAYPAL_CLIENT_SECRET, STRIPE_SECRET_KEY

## 📚 Documentación

### Documentación del proyecto

- [Descripción del Proyecto](docs/DESCRIPCION_PROYECTO.md)
- [Decisiones de Diseño](docs/DECISIONES_DISEÑO.md)
- [TODO](docs/TODO.md)
- [Política de Uso](docs/politica-de-uso.md)

### Documentación API

Una vez iniciado el backend, accede a:
- Swagger UI: http://localhost:3001/api/docs

## 🛠️ Solución de Problemas

### Error de conexión a MySQL

Verifica que:
- MySQL esté corriendo
- La DATABASE_URL sea correcta
- El usuario tenga permisos

**Para MySQL (XAMPP):**
- Asegúrate de que Apache y MySQL estén iniciados en XAMPP
- Verifica las credenciales en phpMyAdmin
- El puerto por defecto es 3306

### Error de conexión a Redis

Verifica que:
- Redis esté corriendo
- Las configuraciones REDIS_HOST y REDIS_PORT sean correctas

### Error de migraciones Prisma

```bash
cd apps/api
npx prisma migrate resolve --applied nombre_migracion
```

### Errores de dependencias

```bash
# Limpiar caché de npm
npm cache clean --force

# Reinstalar dependencias
rm -rf node_modules apps/*/node_modules
npm install
```

## 🤝 Contribución

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -m 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

## 📞 Soporte

Para soporte técnico, envía un email a support@booksmentor.com o abre un issue en el repositorio.

---

**Desarrollado con ❤️ usando NestJS, Next.js y React Native**
