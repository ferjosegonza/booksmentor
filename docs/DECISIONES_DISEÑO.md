# Decisiones de Diseño Confirmadas — BookMentor

## 1. Alta de libros

- El usuario (no solo el admin) puede dar de alta libros
- El sistema abstrae si el libro ya existía o hubo que buscarlo
- "Ingresar un libro" = "suscribirse a un libro" a efectos del límite del plan

## 2. Contenido generado por IA

- Enfoque híbrido: las enseñanzas se generan por IA como borrador
- Requieren aprobación de un admin antes de traducirse/enviarse
- Nunca se reproduce texto original del libro
- Si el admin rechaza una enseñanza, puede regenerarla o editarla manualmente

## 3. Canal de entrega

- El usuario elige: email o in-app
- En mobile: notificación push complementaria (no reemplaza la elección)

## 4. Traducción y generación de enseñanzas

- Comparten el mismo patrón de catálogo rotativo
- Control de cuota y fecha de renovación
- Cache de traducciones para evitar reprocesamiento

## 5. Planes de suscripción

| Plan | Límites |
|---|---|
| **Gratuito** | 1 libro/mes, 1 idioma, frecuencia semanal, 1 canal |
| **Pago** | Sin límite, múltiples idiomas, todas las frecuencias, todos los canales |

## 6. Pagos

- Arquitectura plug-and-play con interfaz `PaymentProvider`
- MercadoPago + PayPal: activos desde el inicio
- Stripe: implementado pero inactivo hasta resolver cuenta desde Argentina

## 7. Autenticación

- Login social con Google (extensible a Apple, Facebook)
- Registro con email/password también disponible

## 8. Política de Uso

- Obligatoria con checkbox en el registro
- Deslinda responsabilidad por uso del contenido generado por IA
- Se registra versión, fecha e IP para trazabilidad legal

## 9. Distribución de carga entre proveedores (round-robin)

- El sistema utiliza un esquema **round-robin** para distribuir el uso entre los proveedores de IA y email activos.
- En cada solicitud, se selecciona el siguiente proveedor disponible según el orden de prioridad definido en el catálogo.
- El orden de rotación es configurable en base de datos y se puede ajustar sin necesidad de desplegar código.
- Esto asegura que ningún proveedor individual soporte toda la carga, espaciando el consumo de cuota y reduciendo el riesgo de agotamiento.