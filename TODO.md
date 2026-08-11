# TODO — BookMentor

---

## 🟢 COMPLETADO
- [x] Crear proyecto Laravel
- [x] Configurar base de datos MySQL
- [x] Crear 16 migraciones
- [x] Crear 7 seeders para catálogos
- [x] Crear 13 modelos
- [x] Ejecutar migraciones y seeders
- [x] Comando `SendDailyTeachings`
- [x] Scheduler configurado

---

## 🔵 PENDIENTE
### Aclaración previa importante
- Toda acción, ya sea nueva funcionalidad, corrección de código, mejora de código u otro, se debe cheuear que exista el correspondiente testing automatizado, correrlo y que se corresponda con el estado actual de los archivos testeados.

### Crear un Dashboard de Admin y otro Dashboard para Usuarios/Clientes
- ABM para todas las tablas que correspondan y gestión de todo lo demás que también corresponda

### Funcionalidades
- Componentes: view, service, controllers
- Crear Controllers para manejar la lógica de negocio
- Crear Views para el frontend con Blade o Livewire
- Implementar autenticación con Laravel Breeze o Jetstream
- Integrar envío de emails con Mailgun/SendGrid

### Publicidad
- Anuncios AdSense estáticos en `index.html` (top, side, bottom)
- Contenedores colapsables para evitar CLS
- Lateral fijo sin desplazar el tablero
- Build verificado sin errores

### Donaciones
- **Cafecito** (Argentina): botón con imagen oficial, intacto, en la fila de botones
- **Ko-fi** (Internacional): botón con imagen oficial SVG, altura coherente, en la fila de botones
- Ambos enlazan a pestaña nueva, sin tapa el tablero ni los controles del juego
- **Responsive**: fila de botones con `flex-wrap`; tamaños compactos en móvil, imagen y normales, imagen
- tener en cuenta "composer require laravel/cashier" sirve para stripe pero como no anda pa argentina lo instalo igual porque sirve para paddle y ese sí sirve para argentina (https://www.paddle.com/ o https://developer.paddle.com/) tener en cuenta que Laravel Cashier no sirve para Ko-fi.

### Header

### Soporte Multilingüe
- Dependencias necesarias.
- Idiomas soportados + `zh-TW`, detección de `navigator.language`, persistencia en `localStorage` y fallback a `en`.
- Implementa selector de idioma con bandera + nombre en desktop y solo globo en móvil.
- Se actualiza dinámicamente `<title>` y metas `description`, `og:` y `twitter:` según el idioma.
- Sincronizar etiquetas metas, footer y todo lo que se deba sincronizar al cambiar idioma.

### Recursos de idioma CJK
- `index.html` ahora incluye la importación de `Noto Sans SC` desde Google Fonts.
- `src/index.css` aplica `Noto Sans SC` mediante variables CSS cuando `html[data-lang="zh"]` o `html[data-lang="zh-TW"]` están activos.

### Accesibilidad
- Optimizar navegación por teclado, para que sea una navegación ágil y fácil de entender ya sea para desktop o móvil, en caso de desktop que contemple tabulación y el buen uso de la tecla Enter.

### Responsive 
- [ ] Revisión integral de todas las resoluciones
- [ ] Verificar LCP, CLS, INP (evitar animaciones costosas)
- [ ] Correr batería completa de tests
- [ ] Build final y verificar compilación

### 📱 Auditoría Responsive — Estado por elemento

> Identifica qué ya tiene comportamiento responsive adecuado y qué falta mejorar.

| Elemento | Estado | Detalle |
|----------|--------|---------|


---

## � TAREAS ADICIONALES PARA DEJAR EL PROYECTO OPERATIVO AL 100%

### Base operativa
- [ ] Configurar variables de entorno y archivo de ejemplo para producción (.env.example, APP_URL, DB, MAIL, QUEUE_CONNECTION, CACHE, SESSION)
- [ ] Implementar autenticación real de usuarios (registro, login, recuperación de contraseña, logout) con roles admin/usuario
- [ ] Crear estructura de rutas, controladores y vistas para usuarios, suscripciones, libros, enseñanzas y sugerencias
- [ ] Definir y aplicar políticas de acceso por rol para proteger paneles y operaciones sensibles
- [ ] Implementar el flujo completo de onboarding al registrarse y elegir plan, frecuencia e idiomas

### Procesos de negocio
- [ ] Crear CRUDs de administración para libros, enseñanzas, traducciones, suscripciones, usuarios y sugerencias
- [ ] Implementar reglas de negocio de planes y límites (máximo de libros, idiomas, audio, etc.)
- [ ] Implementar generación y caché de traducciones para evitar repetir trabajo innecesario
- [ ] Conectar el comando `teachings:send` con envío real de emails y reintentos/colas
- [ ] Configurar el scheduler real y verificar ejecuciones automáticas en producción

### Calidad, operación y lanzamiento
- [ ] Crear tests reales de feature y unit para autenticación, envío de emails, suscripciones y CRUDs críticos
- [ ] Añadir manejo de errores, logs y alertas para fallos de email, base de datos y jobs
- [ ] Preparar despliegue productivo (servidor, workers de cola, cron, SSL, backups y monitoreo)
- [ ] Ejecutar auditoría de UX/UI, accesibilidad, SEO y responsive antes del lanzamiento
- [ ] Documentar pasos de instalación, uso y mantenimiento para desarrolladores y operarios

---

## �📊 LEYENDA

| Símbolo | Significado |
|---------|-------------|
| 🟢 Completado | Tarea terminada y verificada |
| 🟡 En progreso | Tarea comenzada pero no terminada |
| 🔵 Pendiente | Tarea identificada pero no comenzada |
| 🟣 Sugerido | Mejora futura, no planificada |
|  | Checklist item completado |
| ⬜ Pendiente | Checklist item pendiente |
| ❌ Descartado | Descartado por decisión del usuario |

---

## 🐛 BUGS CONOCIDOS (resueltos)

| # | Bug | Estado |
|---|-----|--------|
