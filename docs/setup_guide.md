Aquí tienes la versión anonimizada del archivo, lista para subir a Git. He reemplazado todas las rutas específicas, datos personales y credenciales con placeholders genéricos:

---

```text
# Puesta en marcha local de BookMentor

Esta guía describe el camino más corto para levantar el proyecto en entorno local y probar una entrega dentro de la plataforma. Sólo requiere Node.js, npm, MySQL y el proyecto.

## 1. Qué se va a utilizar

- Node.js 18 o superior.
- npm, porque el repositorio contiene `package-lock.json` y no contiene `pnpm-lock.yaml`.
- MySQL (XAMPP, WAMP, o instalación directa).
- Redis no es necesario para la primera prueba.
- API NestJS en `http://localhost:3001`.
- Frontend Next.js en `http://localhost:3000`.

El correo, los pagos, Google OAuth, las notificaciones push y Redis no son necesarios para la primera prueba. Las entregas están configuradas para leerse dentro de la plataforma.

## 2. Comprobar Node y npm

Abrir una terminal y ejecutar:

```bash
node --version
npm --version
```

Si alguno de los comandos no existe, instalar Node.js LTS y volver a abrir la terminal.

No instalar Turbo globalmente. Turbo ya es una dependencia del proyecto y se ejecuta desde los scripts del monorepo.

## 3. Instalar dependencias

Desde la raíz del proyecto:

```bash
npm install
```

No ejecutar `pnpm install` en este repositorio mientras se conserve `package-lock.json`. Mezclar gestores puede dejar el lockfile y las dependencias en un estado inconsistente.

Si Next falla intentando reparar dependencias SWC con `ENOWORKSPACES`, cerrar los servidores y ejecutar una instalación limpia de dependencias:

```bash
rm -rf node_modules
npm install
```

No borrar `package-lock.json` como primera medida. Si el problema continúa, comprobar que la terminal tenga acceso a `https://registry.npmjs.org`.

## 4. Preparar MySQL

1. Iniciar el servicio de MySQL (XAMPP, WAMP, o servicio del sistema).
2. Acceder a phpMyAdmin o cliente MySQL.
3. Crear una base de datos llamada `booksmentor` con cotejamiento `utf8mb4_unicode_ci`.

La URL de conexión típica es:

```env
DATABASE_URL="mysql://usuario:contraseña@localhost:3306/booksmentor"
```

Ajustar usuario, contraseña y puerto según la configuración local.

## 5. Crear las variables de entorno de la API

Crear el archivo:

```text
apps/api/.env
```

Contenido mínimo para iniciar localmente:

```env
NODE_ENV="development"
PORT=3001
DATABASE_URL="mysql://usuario:contraseña@localhost:3306/booksmentor"
REDIS_HOST="localhost"
REDIS_PORT=6379
REDIS_PASSWORD=""
REDIS_DB=0
USE_REDIS="false"
JWT_SECRET="cambiar-por-secreto-seguro-en-produccion"
JWT_EXPIRES_IN="7d"
FRONTEND_URL="http://localhost:3000"

GEMINI_API_KEY=""
GROQ_API_KEY=""
HUGGINGFACE_API_KEY=""
```

Para esta primera prueba se pueden dejar vacías las claves de IA. La API podrá arrancar, aunque las rutas de generación y traducción no funcionarán hasta configurar un proveedor compatible.

No subir este archivo a Git.

## 6. Modo local sin Redis

La API tiene un modo local sin colas que evita instalar Redis. En `apps/api/.env`, usar:

```env
USE_REDIS="false"
```

Con este valor:

- la API arranca sólo con MySQL;
- BullMQ no se registra;
- las entregas no se programan automáticamente;
- se pueden procesar inmediatamente desde el endpoint de prueba.

No instalar ningún servicio adicional para esta primera prueba.

## 7. Crear el esquema y los datos iniciales

Desde la raíz del proyecto:

```bash
cd apps/api
npx prisma generate
npx prisma migrate dev
npx prisma db seed
```

La migración crea la estructura de tablas y configura las relaciones necesarias. El seed crea planes, idiomas, estados de suscripción, estados de entrega y catálogos de proveedores.

Si Prisma informa que la base no existe, crearla en el cliente MySQL y repetir el comando.

## 8. Crear las variables del frontend

Crear:

```text
apps/web/.env.local
```

Con este contenido:

```env
NEXT_PUBLIC_API_URL="http://localhost:3001"
NEXT_PUBLIC_APP_URL="http://localhost:3000"
```

## 9. Arrancar API y web

Abrir dos terminales.

### Terminal 1: API

```bash
cd apps/api
npm run dev
```

Comprobar:

- API: `http://localhost:3001`
- Swagger: `http://localhost:3001/api/docs`

### Terminal 2: web

```bash
cd apps/web
npm run dev
```

Comprobar:

- Web: `http://localhost:3000`

## 10. Registrar un usuario de prueba

1. Abrir `http://localhost:3000/register`.
2. Completar el formulario y aceptar la política.
3. Iniciar sesión.
4. Confirmar que aparece el dashboard.
5. En Swagger, comprobar que la API responde en `http://localhost:3001/api/docs`.

Si el registro falla, revisar primero MySQL, la URL `DATABASE_URL` y que se haya ejecutado el seed.

## 11. Camino mínimo para probar una entrega en la plataforma

Actualmente el código tiene estas particularidades:

- Crear un libro desde Google Books no crea automáticamente una enseñanza.
- Una suscripción sólo acepta libros con estado `aprobado`.
- Una entrega sólo procesa enseñanzas con estado `aprobado`.
- La revisión administrativa se hace mediante endpoints protegidos.
- La entrega puede programarse con BullMQ si se activa Redis, pero en el modo local simple se procesa manualmente.

Por eso, para probar el ciclo completo sin depender todavía de una IA, se puede crear un libro y una enseñanza de prueba directamente en MySQL.

### 11.1 Convertir el usuario de prueba en administrador

Después de registrar el usuario, ejecutar en el cliente MySQL:

```sql
UPDATE usuarios
SET role = 'administrador'
WHERE email = 'usuario@ejemplo.com';
```

Cerrar sesión y volver a iniciar sesión para obtener un JWT coherente con el rol actualizado.

### 11.2 Crear una enseñanza manual de prueba

Primero consultar los IDs:

```sql
SELECT id, titulo, estado
FROM libros;

SELECT id, nombre
FROM cat_idiomas;
```

Si todavía no hay un libro, usar la pantalla de búsqueda para buscar uno y crearlo desde Google Books. Después consultar su ID.

Insertar una enseñanza aprobada:

```sql
INSERT INTO ensenanzas
  (libro_id, orden, texto_original, tema, estado, version, fecha_generacion)
VALUES
  (1, 1,
   'Esta es una enseñanza de prueba para verificar la lectura dentro de la plataforma.',
   'Prueba local',
   'aprobado',
   1,
   NOW());
```

Actualizar el contador del libro:

```sql
UPDATE libros
SET cantidad_ensenanzas = 1,
    estado = 'aprobado'
WHERE id = 1;
```

Cambiar `1` por el ID real del libro si es diferente.

### 11.3 Crear la suscripción

En la web, buscar el libro local, seleccionar un idioma y suscribirse. También se puede usar Swagger:

```http
POST /api/subscriptions
Authorization: Bearer <TOKEN>
Content-Type: application/json

{
  "bookId": 1,
  "idiomaIds": [1]
}
```

El `bookId` y el idioma deben existir.

### 11.4 Ejecutar la entrega inmediatamente

Con `USE_REDIS="false"`, usar Swagger directamente:

```http
POST /api/deliveries/1/process-now
Authorization: Bearer <TOKEN>
```

Cambiar `1` por el ID real de la suscripción. El endpoint genera la traducción si corresponde, registra la entrega y actualiza el progreso. Después abrir nuevamente el dashboard: la enseñanza debería aparecer en **Enseñanzas recibidas**.

El endpoint anterior es para una prueba local. La cola programada sigue disponible opcionalmente para una instalación que configure Redis con `USE_REDIS="true"`.

## 12. Camino con una API gratuita de IA

Cuando el ciclo manual funcione, configurar sólo un proveedor. No configurar los tres al mismo tiempo en la primera prueba.

En `apps/api/.env`, añadir una clave válida:

```env
GEMINI_API_KEY="tu-clave-api"
```

O:

```env
GROQ_API_KEY="tu-clave-api"
```

O:

```env
HUGGINGFACE_API_KEY="tu-clave-api"
```

Reiniciar la API después de modificar `.env`.

Importante: el código actual usa identificadores antiguos de modelos, entre ellos `gemini-pro`, `llama2-70b-4096` y `meta-llama/Llama-2-70b-chat-hf`. Es posible que una clave válida no alcance porque el proveedor ya no ofrezca esos modelos. Antes de probar generación real hay que actualizar el identificador del modelo en `apps/api/src/modules/ai/ai.service.ts` según el modelo vigente del proveedor elegido.

La estrategia más sencilla es:

1. Elegir un solo proveedor.
2. Actualizar su modelo en `ai.service.ts`.
3. Configurar sólo su clave.
4. Reiniciar la API.
5. Probar `POST /api/ai/generate-teaching` desde Swagger.
6. Guardar el resultado como enseñanza aprobada o implementar el flujo administrativo correspondiente.

## 13. Lo que no hace falta configurar todavía

Para probar lectura interna no son necesarios:

- Resend, Brevo o AWS SES.
- MercadoPago, PayPal o Stripe.
- Google OAuth.
- Expo Push Notifications.
- La aplicación mobile.
- Un servidor de producción.
- Redis.

El método de entrega actual es `in_app`. El código de correo permanece como integración antigua, pero no se usa en el procesamiento normal de entregas.

## 14. Orden recomendado de diagnóstico

Si algo falla, comprobar en este orden:

1. `node --version` y `npm --version`.
2. `npm install` desde la raíz.
3. MySQL iniciado.
4. Base `booksmentor` creada.
5. Archivo `apps/api/.env` correcto.
6. `USE_REDIS="false"` en `apps/api/.env`.
7. `npx prisma generate`.
8. `npx prisma migrate dev`.
9. `npx prisma db seed`.
10. API iniciada en el puerto `3001`.
11. Web iniciada en el puerto `3000`.
12. Registro y login.
13. Libro aprobado y enseñanza aprobada.
14. Suscripción creada.
15. Endpoint `POST /api/deliveries/:subscriptionId/process-now` ejecutado.

## 15. Estado real del proyecto

La infraestructura local y la lectura interna están preparadas, pero todavía quedan tareas de producto para que el flujo sea cómodo:

- Generar y persistir automáticamente una enseñanza al crear un libro nuevo.
- Crear una pantalla de administración para aprobar enseñanzas.
- Agregar un endpoint explícito de entrega inmediata para pruebas.
- Actualizar los modelos de IA antiguos.
- Reemplazar el método de notificación push, que actualmente sólo escribe un log.
- Añadir pruebas automatizadas del ciclo libro -> enseñanza -> aprobación -> suscripción -> entrega.
```
