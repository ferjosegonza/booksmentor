# Descripción del Proyecto — Sistema de Envío de Enseñanzas por Suscripción

> ⚠️ **Nota sobre los límites de free tier citados en este documento:** todos los proveedores gratuitos mencionados (email, IA, libros) modifican sus límites con frecuencia y sin aviso. Las cifras aquí son las vigentes al momento de este análisis y sirven como criterio de diseño (por eso el sistema usa un **catálogo rotativo configurable en base de datos** y no límites hardcodeados). Antes de cada despliegue a producción hay que verificar los valores actuales en la documentación oficial de cada proveedor.

---

## 1. Resumen Ejecutivo

BookMentor es una plataforma web y móvil que permite a los usuarios recibir enseñanzas extraídas de libros de forma personalizada y progresiva. El usuario da de alta libros simplemente tipeando su nombre —el sistema verifica que existan y los incorpora al catálogo de forma transparente—, se suscribe a ellos, y recibe una enseñanza por vez, traducida al idioma que elija, por email o dentro de la app (con notificación push si usa la versión mobile).

**Principios rectores:**
- No reproduce ni almacena texto protegido de los libros
- Las enseñanzas son borradores generados por IA, sujetos a revisión administrativa
- El usuario acepta una Política de Uso responsable al registrarse
- Arquitectura multi-proveedor para IA, email y pagos (fallback automático)

---

## 2. Actores del Sistema

| Actor | Rol |
|-------|-----|
| **Usuarios registrados** | Se registran (aceptando obligatoriamente la Política de Uso), dan de alta libros tipeando su nombre —el sistema los busca, confirma que existan y los carga sin que el usuario deba saber si ya estaban en la base o no—, eligen idioma(s) de recepción, frecuencia, y canal (email o in-app + push). |
| **Administradores** | Intervención solo bajo necesidad: gestionan catálogo de libros/idiomas/planes, **revisan y aprueban** las enseñanzas generadas automáticamente antes de que se envíen a usuarios, y dan seguimiento a las sugerencias/feedback. |
| **Sistema (automático)** | Procesa envíos programados, genera enseñanzas y traducciones mediante el catálogo rotativo de IAs, controla cuotas/tokens y sus fechas de renovación, actualiza estados de suscripción y de contenido. |

---

## 3. Flujo de Alta de un Libro (transparente para el usuario)

1. El usuario tipea el nombre de un libro. El sistema dispara autocompletado consultando en paralelo: (a) la tabla local `libros` y (b) la **Google Books API**.
2. Se muestran sugerencias (título, autor, año, portada) para que el usuario confirme cuál es el libro que quiere.
3. **Si ya existe en la base** → se reutiliza esa entrada (y su cache de enseñanzas/traducciones ya generadas, evitando reprocesamiento).
4. **Si no existe** → se crea automáticamente. El usuario nunca necesita saber si el libro ya estaba cargado o es nuevo; solo ve "libro agregado a tu lista".
5. "Dar de alta un libro" y "suscribirse a un libro" son, a todos los efectos de plan/límite, la misma acción.
6. Si es un libro nuevo, se dispara la generación de sus enseñanzas en estado `borrador_ia_pendiente_revision`.

## 3.1 Flujo detallado de búsqueda y carga de un libro

Cuando un usuario busca un libro, el sistema sigue este flujo:

1. **Búsqueda en base de datos propia**:
   - El sistema consulta la tabla `Libros` buscando por título, autor e ISBN.
   - Si encuentra el libro, va al paso 3.

2. **Si NO existe en la base de datos**:
   - Consulta Google Books API para obtener metadatos (título, autor, año, portada).
   - Presenta sugerencias al usuario para confirmar el libro.
   - Crea el libro en la base de datos con estado `borrador_ia_pendiente_revision`.
   - Dispara la generación de enseñanzas con IA (resúmenes en palabras propias).
   - Las enseñanzas quedan pendientes de aprobación por un administrador.
   - **El proceso termina aquí hasta que el admin apruebe las enseñanzas.**

3. **Si existe en la base de datos y tiene enseñanzas aprobadas**:
   - El sistema identifica el/los idioma(s) que pidió el usuario.
   - Para cada idioma solicitado, consulta la tabla `Traducciones`.
   - **Si la traducción existe**: la reutiliza (no consume cuota de IA).
   - **Si la traducción NO existe**: genera SOLO esa traducción usando IA, la almacena en `Traducciones` y la reutiliza para futuros usuarios.
   - **NUNCA** se regeneran enseñanzas originales si el libro ya existe y tiene enseñanzas aprobadas.

4. **El usuario se suscribe al libro**:
   - Una vez que el libro tiene enseñanzas aprobadas y traducciones disponibles en los idiomas solicitados, el usuario puede suscribirse.
   - Esto ocurre de forma transparente para el usuario; él solo ve "libro agregado a tu lista".
---

## 4. Origen del Contenido — Enfoque Híbrido

**Problema de fondo:** las APIs de libros (Google Books, Open Library) solo devuelven metadata (título, autor, año, portada), nunca el texto completo. Y reproducir fragmentos del libro original expondría al sistema a problemas de derechos de autor.

**Solución adoptada (híbrida):**

- Las enseñanzas de un libro nuevo se generan mediante consultas al mismo **catálogo rotativo de IAs gratuitas** que se usa para traducción, pidiéndole explícitamente que **resuma ideas y conceptos en palabras propias**, nunca que cite o reproduzca texto original.
- Toda enseñanza generada automáticamente queda en estado `generado_por_ia_pendiente_revision` y **no se envía a ningún usuario** hasta que un administrador la revisa y la pasa a `aprobado`.
- Si el admin la rechaza, puede regenerarla con otra IA del catálogo o editarla manualmente.
- Este enfoque preserva la automatización pedida (el usuario no percibe fricción) agregando una salvaguarda de calidad y legal antes de que el contenido llegue a un usuario final.

---

## 5. Catálogo Rotativo de IA (traducción y generación de enseñanzas)

### Por qué un catálogo rotativo y no un solo proveedor

Todos los servicios de IA con capa gratuita imponen límites (tokens, peticiones por minuto, peticiones por día) que un sistema con muchos usuarios y libros puede agotar rápido. En vez de pagar desde el día uno, el sistema **itera automáticamente** entre varios proveedores gratuitos: al agotarse la cuota de uno, pasa al siguiente sin intervención manual, y vuelve a activarlo automáticamente cuando el proveedor renueva su cuota.

### Diseño técnico

- Tabla `ai_catalog`: proveedor, tipo de uso (traducción / generación de enseñanzas), tokens consumidos, límite del free tier, **fecha/hora de próxima renovación**, prioridad, estado (`activo` / `agotado` / `inactivo`).
- Un job periódico revisa las entradas `agotado` y las reactiva cuando se cumple la fecha de renovación registrada.
- El orden de proveedores es configurable en base de datos, no hardcodeado en el código — se puede reordenar sin deploy.

### Candidatos evaluados y por qué

| Proveedor | Por qué se considera | Orden de límite gratuito (referencial) |
|---|---|---|
| **Google Gemini API (free tier)** | Buena calidad de resumen/traducción multilingüe, tier gratuito generoso pensado para prototipos, se integra fácil con Google OAuth que ya usamos para login. | Del orden de decenas de peticiones por minuto y un límite diario de tokens/peticiones. |
| **Groq (free tier)** | Inferencia extremadamente rápida (hardware LPU), free tier pensado para desarrollo, útil como "segundo proveedor" cuando Gemini se agota. | Límite por tokens-por-minuto y peticiones-por-día. |
| **Hugging Face Inference API (free tier)** | Acceso gratuito a múltiples modelos open-source, buen "tercer proveedor" de respaldo, sin necesidad de tarjeta de crédito. | Límite de créditos/peticiones mensuales para uso serverless gratuito. |

La arquitectura permite agregar o quitar proveedores (ej. Cohere, Mistral, OpenRouter con modelos gratuitos) sin tocar la lógica de negocio, ya que todos implementan una interfaz común `AiProvider`.

### Traducciones

- Cache por combinación (enseñanza, idioma destino): si una enseñanza ya fue traducida al idioma que pide un nuevo usuario, se reutiliza sin volver a consumir cuota de IA.

---

## 6. Canal de Entrega

- Por cada suscripción, el usuario elige: **leer dentro de la app** o **recibir por email**.
- Si usa la versión mobile, además recibe una **notificación push** avisando que ya tiene una traducción lista (complementa, no reemplaza, la elección anterior).

### Catálogo rotativo de email (mismo patrón que la IA)

| Proveedor | Por qué se considera | Límite gratuito (referencial) |
|---|---|---|
| **Resend** | SDK moderno, buena entregabilidad, pensado para apps con envíos transaccionales. | Tier gratuito con un límite mensual de envíos (del orden de miles/mes). |
| **Brevo (ex-Sendinblue)** | Alternativa con límite **diario** en vez de mensual, útil como segundo proveedor. | Límite diario de envíos en su plan gratuito (históricamente unos cientos por día). |
| **Amazon SES** | Muy económico incluso fuera del free tier, ideal como tercer nivel de respaldo. | Free tier de aproximadamente unos miles de mensajes por mes durante el primer año. |

Igual que con las IAs: tabla `email_catalog` con estado, cuota consumida y fecha de renovación, con fallback automático al siguiente proveedor.

### Push notifications

- **Expo Push Notifications**: gratuito, sin límites prácticos para este caso de uso, e integrado nativamente si el mobile se construye con React Native + Expo (evita tener que gestionar certificados APNs/FCM por separado).

---

## 7. Pagos — Arquitectura Multi-Proveedor

### El problema a resolver

Se pidió integrar las plataformas de pago más usadas en EEUU, Europa y Asia, con la particularidad de que **Stripe no está habilitado para cuentas de Argentina**. Habilitar Stripe desde Argentina (vía una entidad extranjera, un socio en Europa, Stripe Atlas, etc.) es una **decisión de negocio/legal/impositiva**, no una decisión técnica — por eso se separa claramente del diseño de software.

### Solución: interfaz `PaymentProvider`

Se define una interfaz genérica que todo proveedor de pago debe cumplir:
PaymentProvider {
createSubscription()
cancelSubscription()
handleWebhook()
refund()
}

El resto del sistema (planes, suscripciones, facturación) solo interactúa con esta interfaz, nunca con el SDK de un proveedor específico. Agregar, quitar o priorizar un proveedor no requiere tocar la lógica de negocio.

### Proveedores y su estado inicial

| Proveedor | Cobertura | Estado inicial | Motivo |
|---|---|---|---|
| **MercadoPago** | Fuerte en Argentina y LatAm | ✅ Activo | Es el único de los tres operable directamente desde una cuenta argentina sin intermediarios. |
| **PayPal** | Prácticamente global (EEUU, Europa, buena parte de Asia) | ✅ Activo | Disponible para cuentas argentinas (con algunas restricciones de retiro, pero operable para cobrar). |
| **Stripe** | El más usado en EEUU, Europa y gran parte de Asia | ⏸️ Implementado pero **inactivo** | No habilitado para cuentas argentinas. Código y webhook completos desde el día uno; se activa con un flag en base de datos el día que se resuelva la situación de la cuenta. |

La tabla `payment_providers` guarda: proveedor, `activo`, `prioridad`, `region`. Esto permite, por ejemplo, mostrarle MercadoPago primero a un usuario que paga desde Argentina y Stripe primero a uno que paga desde Europa, el día que se active.

---

## 8. Política de Uso / Condiciones de Aceptación

Dado que parte del contenido es generado por IA y puede contener imprecisiones, el registro exige que el usuario **tilde un checkbox** aceptando una Política de Uso antes de crear la cuenta. El texto (a redactar en `docs/politica-de-uso.md`) debe cubrir, como mínimo:

- El contenido puede ser generado por inteligencia artificial y contener errores, imprecisiones o interpretaciones inexactas del libro original.
- El usuario es responsable del uso que le da al contenido recibido (por ejemplo, no debe sustituir asesoramiento profesional en salud, finanzas, legal, u otras áreas sensibles).
- El sistema y su operador no asumen responsabilidad por decisiones tomadas en base al contenido entregado.

Se registra en `user_legal_acceptance`: usuario, versión de la política aceptada, fecha y hora — para trazabilidad legal si la política cambia en el futuro.

---

## 9. Planes de Suscripción (simplificado)

| Plan | Alcance |
|---|---|
| **Gratuito** | 1 libro por mes, 1 idioma de traducción, frecuencia fija semanal, un solo canal de entrega (email **o** push, no ambos). |
| **Pago** | Sin límite de libros simultáneos (según el nivel contratado), múltiples idiomas por libro, todas las frecuencias disponibles (diaria, laborables, semanal, mensual), email + in-app + push combinados. |

El administrador clasifica la cuenta de cada usuario (plan asignado), y ese plan determina el límite de libros/suscripciones simultáneas.

---

## 10. Justificación del Stack Tecnológico

| Capa | Elección | Por qué esta y no otra |
|---|---|---|
| **Backend** | Node.js + NestJS (TypeScript) | Arquitectura modular por diseño (coincide 1 a 1 con los 8 módulos del sistema: usuarios, libros, IA, traducciones, suscripciones, envíos, pagos, feedback), tipado fuerte reduce errores en una base de datos con ~16 tablas relacionadas, y tiene soporte de primera clase para colas (BullMQ), OAuth y webhooks de pago. |
| **Base de datos** | MySQL 8.0+ | Gratuita, madura, excelente manejo de relaciones e integridad referencial — crítico acá porque hay múltiples catálogos (idiomas, planes, estados) relacionados entre sí. Soporta bien el patrón "catálogo con estado y fecha de renovación" usado tanto para IA como para email. |
| **ORM** | Prisma | Migraciones versionadas y legibles, lo que facilita mantener el TODO.md alineado con el estado real del esquema a medida que se agregan tablas. |
| **Frontend web** | Next.js (React) + Tailwind CSS | Permite una web "bonita" con buen rendimiento y SEO (útil si en el futuro se quiere captar usuarios por búsqueda orgánica), y comparte tipos/lógica con el resto del ecosistema TypeScript del proyecto. |
| **Mobile** | React Native (Expo) | Una sola base de código para iOS y Android, reutiliza gran parte de la lógica de negocio del frontend web (mismo lenguaje), y **Expo Notifications** resuelve las notificaciones push sin gestionar certificados APNs/FCM manualmente — clave porque el pedido explícito era push gratuito y simple. |
| **Autenticación** | Auth.js / Passport + Google OAuth | Cubre el pedido de login social, y es extensible a otros proveedores (Apple, Facebook) sin rediseñar el módulo. |
| **Cola de envíos programados** | BullMQ + Redis | Gratuito, liviano, permite reintentos automáticos si un envío falla (por ejemplo si un proveedor de email se agotó a mitad de un lote), y corre en cualquier VPS económico o contenedor. |
| **Email** | Catálogo rotativo Resend → Brevo → Amazon SES | Mismo patrón de "catálogo con fallback" que la IA: maximiza el uso de free tiers combinando límites mensuales (Resend, SES) con un límite diario distinto (Brevo), reduciendo la chance de quedarse sin cupo de envío. |
| **Catálogo de IA** | Google Gemini → Groq → Hugging Face (free tiers) | Combina calidad (Gemini), velocidad (Groq) y redundancia sin tarjeta de crédito (Hugging Face) — con fallback automático por agotamiento de tokens, que era un requisito explícito. |
| **Búsqueda/autocompletado de libros** | Google Books API | Cobertura mundial muy amplia, no requiere API key para búsquedas básicas (reduce fricción y costo), y da los metadatos justos y necesarios (título, autor, año) para identificar el libro sin necesitar su texto completo. |
| **Pagos** | Interfaz `PaymentProvider` (MercadoPago + PayPal activos, Stripe listo e inactivo) | Resuelve simultáneamente la cobertura mundial pedida y la restricción real de operar desde Argentina, sin bloquear el desarrollo ni atarse a una única decisión de negocio. |

---

## 11. Cache y Reutilización (transversal a todo el sistema)

Toda extracción de enseñanzas y toda traducción se guarda de forma permanente, indexada por libro/idioma. Si otro usuario se suscribe a un libro ya procesado, o pide el mismo idioma ya traducido, el sistema reutiliza lo existente en vez de volver a consumir cuota de IA — esto es lo que hace viable operar mayormente con proveedores gratuitos a medida que crece la base de libros.

---

## 12. Flujo de Suscripción y Envío

1. Usuario se suscribe a un libro aprobado
2. Elige idioma(s), frecuencia y canal (email/in-app)
3. Sistema programa el primer envío según zona horaria y frecuencia
4. BullMQ/Redis ejecuta el job en la fecha programada
5. Obtiene la siguiente enseñanza no enviada para esa suscripción
6. Traduce al idioma elegido (usando caché si existe)
7. Envía por email (con fallback entre proveedores) y/o push
8. Registra en `Historial_Envíos`
9. Actualiza `Última_enseñanza_enviada` y calcula nuevo `Porcentaje_avance`
10. Si es la última enseñanza → la suscripción pasa a `Completado`
11. Si no → programa el siguiente envío

---

## 13. Riesgos y Mitigaciones

| Riesgo | Mitigación |
|--------|------------|
| Límites de free tier de IA se reducen | Catálogo rotativo configurable desde DB; alertas de agotamiento |
| Google Books cambia su API | Caché de metadatos; búsqueda local primero |
| Stripe no se puede usar desde Argentina | PayPal + MercadoPago activos desde el día 1 |
| Proveedor de email bloquea dominios | Fallback automático al siguiente proveedor |
| Contenido IA contiene errores | Revisión administrativa obligatoria antes de envío |
| Usuario usa contenido para fines indebidos | Política de Uso firmada en registro; avisos en cada envío |

---

## 14. Métricas de Éxito

- Tiempo de respuesta de IA < 10s
- 99% de entregas de email
- Tasa de retención > 60% a los 30 días
- Tiempo de carga de páginas < 2s