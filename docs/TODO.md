# TODO — BookMentor

> Documento de control de avance. Una tarea sólo pasa a **Completado** cuando su implementación, pruebas automatizadas y documentación asociada estén verificadas.

---

## Estado actual

| Estado | Alcance |
|---|---|
| Completado | Documentación inicial consolidada: descripción del proyecto y este tablero. |
| Pendiente | Todo código, esquema de datos, integraciones y pruebas de la arquitectura objetivo. |

---

## ✅ / ⬜ Checklist de Avance

### Fase 0 — Documentación y preparación base

- [x] Consolidar requisitos, aclaraciones y límites del producto
- [x] Definir arquitectura objetivo: NestJS, MySQL/Prisma, Next.js, React Native/Expo, Redis/BullMQ
- [x] Documentar política de no reproducción y revisión de contenido IA
- [x] Definir criterio de proveedores intercambiables para IA, email y pagos
- [ ] Crear estructura del monorepo (`apps/api`, `apps/web`, `apps/mobile`, `packages/shared`, `docs/`)
- [x] Crear `docs/DESCRIPCION_PROYECTO.md` con justificación tecnológica y flujos de usuario
- [x] Crear este TODO.md
- [ ] Crear `README.md` con instrucciones de instalación, desarrollo, pruebas y despliegue
- [ ] Redactar `docs/politica-de-uso.md` (texto legal para checkbox de registro)
- [ ] Definir entornos (`local`, `staging`, `production`), secretos, dominio y correo remitente
- [ ] Definir presupuesto, proveedores contratables y países objetivo antes de activar producción

---

### Fase 1 — Modelo de datos (Prisma)

#### Catálogos base
- [ ] `Cat_Planes` — Gratuito, Básico, Pro, Premium (límites configurables)
- [ ] `Cat_Idiomas` — Español, Inglés, Portugués, Italiano, Francés (códigos ISO)
- [ ] `Cat_Frecuencias` — Diaria, Solo laborables, Semanal, Mensual (días entre envíos)
- [ ] `Cat_Estados_Suscripcion` — Activo, Completado, Pausado
- [ ] `Cat_Estados_Envio` — Pendiente, Entregado, Rebotado, Abierto, Fallido
- [ ] `Cat_Tags` — Etiquetas temáticas para libros

#### Catálogos de proveedores (rotativos)
- [ ] `AiCatalog` — proveedor, tipo de uso, límite, consumo, fecha renovación, prioridad, estado
- [ ] `EmailCatalog` — mismo patrón aplicado a proveedores de email
- [ ] `PaymentProviders` — proveedor, activo, prioridad, región

#### Entidades principales
- [ ] `Usuarios` — email, nombre, frecuencia_id, plan_id, hora_envío, zona_horaria, fecha_registro, activo
- [ ] `UserLegalAcceptance` — usuario_id, versión política, fecha/hora, IP
- [ ] `Libros` — título, autor, idioma_original_id, año_publicación, cantidad_enseñanzas, estado
- [ ] `Libro_Tags` — relación N:N entre libros y tags
- [ ] `Enseñanzas` — libro_id, orden, texto_original, tema
- [ ] `Traducciones` — enseñanza_id, idioma_id, texto_traducido, fecha_traducción, veces_usado, último_uso
- [ ] `Suscripciones` — usuario_id, libro_id, estado_id, última_enseñanza_enviada, fecha_próximo_envío
- [ ] `Suscripcion_Idiomas` — relación N:N entre suscripciones e idiomas
- [ ] `Historial_Envíos` — usuario_id, enseñanza_id, idioma_id, estado_id, fecha_envío
- [ ] `Sugerencias_Usuarios` — usuario_id, email, tipo_id, libro_sugerido, mensaje, leído, atendido, fecha_envío, respuesta_admin

#### Auditoría y seguridad
- [ ] Auditoría de uso de IA: proveedor, modelo, cuota, tokens, solicitud, resultado, error
- [ ] Caché única de traducciones por contenido + idioma + versión de prompt
- [ ] Índices, claves únicas y estrategia de retención de datos
- [ ] Cifrado de credenciales de proveedores

---

### Fase 2 — Backend Core (NestJS)

#### Módulo de autenticación
- [ ] Registro con email/password + validación
- [ ] Inicio de sesión con Google OAuth (dejar arquitectura lista para Apple)
- [ ] Checkbox obligatorio de Política de Uso en registro
- [ ] Almacenamiento de versión, fecha e IP en `UserLegalAcceptance`

#### Módulo de usuarios y preferencias
- [ ] Perfil de usuario (nombre, email, zona horaria)
- [ ] Configuración de frecuencia de envío
- [ ] Configuración de canal de entrega (email / in-app)
- [ ] Roles: `usuario` y `administrador`

#### Módulo de catálogo de libros
- [ ] Búsqueda local + autocompletado con Google Books API
- [ ] Normalización y deduplicación de libros (por ISBN, título, autor)
- [ ] Creación automática de libro cuando el usuario lo da de alta
- [ ] Generación en cola de enseñanzas con IA (`borrador_ia_pendiente_revision`)

#### Módulo de orquestación IA (fallback rotativo)
- [ ] Interfaz `AiProvider` + adaptadores: Gemini, Groq, Hugging Face
- [ ] Selección por prioridad, capacidad, estado del proveedor
- [ ] Medición de cuota antes/después de cada llamada
- [ ] Registro de límites, renovación, consumo y respuesta
- [ ] Suspensión temporal ante `429`, cuota agotada, credenciales inválidas
- [ ] Reactivación automática al llegar fecha de renovación
- [ ] Reintentos con backoff y circuit breaker
- [ ] Cache de traducciones por (enseñanza, idioma destino)
- [ ] Prompts estructurados con advertencias temáticas

#### Módulo de suscripciones
- [ ] Alta de suscripción a un libro (con validación de límites del plan)
- [ ] Pausa y reanudación
- [ ] Finalización automática al completar todas las enseñanzas
- [ ] Cálculo de progreso y próxima fecha de envío
- [ ] Selección de idioma(s) por suscripción
- [ ] Límites de plan parametrizados (no hardcodeados)

#### Módulo de envíos programados
- [ ] Interfaz `EmailProvider` + adaptadores: Resend, Brevo, Amazon SES
- [ ] Interfaz `PushProvider` + adaptador: Expo Notifications
- [ ] Jobs BullMQ/Redis para envíos programados
- [ ] Scheduler respetando zona horaria y frecuencia del usuario
- [ ] Deduplicación de envíos (evitar duplicados)
- [ ] Registro completo en `Historial_Envíos`
- [ ] Procesamiento de bounces y webhooks de proveedores

#### Módulo de pagos
- [ ] Interfaz `PaymentProvider` (createSubscription, cancel, refund, handleWebhook)
- [ ] Implementación MercadoPagoProvider (activo)
- [ ] Implementación PayPalProvider (activo)
- [ ] Implementación StripeProvider (implementado, inactivo por restricción de cuenta)
- [ ] Webhooks idempotentes con verificación de firmas
- [ ] Facturación/recibos y conciliación de estados

#### Módulo de sugerencias
- [ ] Envío de sugerencias por usuarios
- [ ] Seguimiento administrativo (leído, atendido, respuesta)

#### Panel de administración
- [ ] Revisión y aprobación/rechazo de enseñanzas generadas por IA
- [ ] Gestión de catálogos (libros, idiomas, planes, tags)
- [ ] Dashboard de proveedores (IA, email, pagos) y cuotas
- [ ] Gestión de sugerencias de usuarios
- [ ] Trazabilidad y versionado de enseñanzas

---

### Fase 3 — Frontend Web (Next.js)

- [ ] Registro/login (con checkbox obligatorio de Política de Uso)
- [ ] Buscador de libros con autocompletado (Google Books API)
- [ ] Panel de suscripciones: ver, agregar, pausar, reanudar
- [ ] Visualización de progreso por libro
- [ ] Lectura de enseñanzas in-app (historial completo)
- [ ] Configuración de preferencias (idioma, frecuencia, canal)
- [ ] Panel de administración (revisión de enseñanzas, gestión de catálogos)
- [ ] Diseño responsive y accesible
- [ ] Estados de carga, error y contenido pendiente de revisión

---

### Fase 4 — Mobile (React Native + Expo)

- [ ] Registro/login (con checkbox obligatorio de Política de Uso)
- [ ] Buscador de libros y gestión de suscripciones
- [ ] Lectura de enseñanzas in-app
- [ ] Notificaciones push (Expo Notifications)
- [ ] Permisos explícitos, registro/desregistro de dispositivos
- [ ] Deep links a la lectura
- [ ] Estados de carga, error y contenido pendiente de revisión

---

### Fase 5 — Calidad, Operación y Lanzamiento

#### Pruebas
- [ ] Unitarias para módulos principales
- [ ] Integración: OAuth, aceptación legal, límites de plan, deduplicación
- [ ] Cuotas IA, caché de traducciones, programación de envíos
- [ ] Webhooks de pago y correo/push

#### Calidad de código
- [ ] Lint, typecheck, pruebas en CI
- [ ] Análisis de dependencias
- [ ] Migraciones versionadas

#### Observabilidad
- [ ] Logs estructurados
- [ ] Métricas y alertas
- [ ] Trazas de jobs y panel de cuotas

#### Legal
- [ ] Revisión legal profesional de Términos, Privacidad, tratamiento de datos, contenido IA

#### Despliegue
- [ ] HTTPS, workers, cron, Redis, MySQL
- [ ] Backups restaurables y plan de recuperación
- [ ] Documentación de despliegue low-cost

#### Pre-lanzamiento
- [ ] Verificar límites de free tier vigentes de cada proveedor
- [ ] Alertas para cuando todo el catálogo de IA/email/pagos está agotado
- [ ] Pruebas de carga, seguridad y accesibilidad
- [ ] Revisión final para tiendas móviles

---

## Regla Continua de Calidad

- [ ] Para cada cambio futuro: actualizar este tablero
- [ ] Crear/ajustar pruebas automatizadas pertinentes
- [ ] Ejecutar pruebas y registrar el resultado antes de marcar como completado