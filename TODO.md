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
- Componentes: migrations, models, view, service, controllers
- Laravel scheduler para gestionar el envío de enseñanzas traducidas en los tiempos agendados por el usuario.
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

### Recursos de idioma CJK — 
- `index.html` ahora incluye la importación de `Noto Sans SC` desde Google Fonts.
- `src/index.css` aplica `Noto Sans SC` mediante variables CSS cuando `html[data-lang="zh"]` o `html[data-lang="zh-TW"]` están activos.

### O8: Accesibilidad
- Optimizar navegación por teclado, para que sea una navegación ágil y fácil de entender ya sea para desktop o móvil, en caso de desktop que contemple tabulación y el buen uso de la tecla Enter.
- [ ] **O8-C2**: Corregir contraste de texto (`text-slate-500` → WCAG AA), verificar slots vacíos
- [ ] **O8-C3**: `aria-label` en cartas/botones faltantes
- [ ] **O8-C4**: `prefers-reduced-motion` global (ya implementado parcialmente en index.css)

### O9: Sonido ambiental de lluvia (ligero)
- [ ] **O9-C1**: Hook `useRainSound` (Howler/useSound) con loop, activo solo en modo rainy
- [ ] **O9-C2**: Bajo volumen, sin romper `useSoundEffects` existente
- [ ] **O9-C3**: Sonido gratuito corto (loopable) tipo "lluvia suave"

### O10: Responsive final + Core Web Vitals + limpieza
- [ ] **O10-C1**: Revisión integral de todas las resoluciones (360px → ultrawide)
- [ ] **O10-C2**: Verificar LCP, CLS, INP (evitar animaciones costosas)
- [ ] **O10-C3**: Correr batería completa de tests
- [ ] **O10-C4**: Build final (`npm run build`) y verificar compilación

### 📱 Auditoría Responsive — Estado por elemento

> Identifica qué ya tiene comportamiento responsive adecuado y qué falta mejorar.

| Elemento | Estado | Detalle |
|----------|--------|---------|
| Cartas (ancho) | ✅ | `--card-w: clamp(36px, 11vw, 78px)` se adapta en todas las resoluciones |
| Alto de columnas | ✅ | `getColumnHeight` dinámico, no escapa del recuadro |
| Tablero | ✅ | `game-board` con `p-2 sm:p-4`, grid `gap-1 sm:gap-2` |
| Grid 7 columnas | ✅ | `grid-cols-7` fijo; las cartas se encogen vía `clamp` |
| Layout Home | ✅ | `flex-col lg:flex-row`, `px-3 py-5 sm:py-7`, `max-w-[1100px]` |
| Header pill/título | ✅ | `text-center sm:text-left`, títulos `text-2xl sm:text-3xl` |
| Botones donación (Ko-fi/Cafecito) | ✅ | `flex-wrap` en la fila; compactos en móvil (`min-h-[52px]`, img `h-[34px]`), normales en `sm+` |
| Fila de botones | ✅ | `flex-wrap items-center justify-end gap-2` |
| Botón sonido | ✅ | `min-h-[52px] sm:min-h-[64px]`, `px-3 sm:px-4` |
| Botón New Game | ✅ | `text-base sm:text-lg`, `px-4 sm:px-6`, sin `min-w` rígido |
| Overlay de victoria | ✅ | `px-4`, texto responsivo |
| Details "How to play" | ✅ | `text-sm`, lista fluida |
| Anuncios (top/bottom/side) | ✅ | `ad-container` responsive, lateral solo ≥1200px, colapso con `:has()` |
| Footer y páginas (Privacy/Contact) | ⚠️ | No verificados en detalle a 360px; revisar como parte de O10-C1 |
| Ultrawide (>1920px) | ⚠️ | El tablero queda centrado con `max-w-[1100px]`; validar que no se vea "perdido" ni se dispare el lateral |

**Pendiente de mejora responsive (para O10):**
- [ ] Revisar footer y páginas administrativas (Privacy/Contact) en 360px–390px
- [ ] Validar composición en ultrawide (1920px+): el tablero centrado a 1100px puede sentirse pequeño; evaluar `max-w` mayor o fondo decorativo
- [ ] Confirmar que el anuncio lateral fijo (160px) no genere solapamiento con el contenido en 1200–1400px
- [ ] Verificación visual de la fila de botones en 360px (que no queden 2 botones partidos de forma fea)

---

## 📊 LEYENDA

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
