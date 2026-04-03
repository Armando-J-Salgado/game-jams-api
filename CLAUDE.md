# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Game Jams API — A Laravel 13 REST API for managing game jam competitions, teams, and submissions. Built with Sanctum authentication and spatie/laravel-permission for role-based authorization.

## Rúbrica del Proyecto de Cátedra: Diseño y Desarrollo de API REST

Este documento describe los lineamientos, entregables y criterios de evaluación del proyecto final de la asignatura. El objetivo central es que el estudiante demuestre su capacidad para diseñar, documentar e implementar una API funcional bajo el framework Laravel, integrando buenas prácticas de desarrollo, seguridad y calidad de código.

---

### Información General

| Campo          | Detalle                                                                                        |
| -------------- | ---------------------------------------------------------------------------------------------- |
| **Objetivo**   | Diseñar e implementar una API REST completa con Laravel, demostrando dominio técnico integral. |
| **Alcance**    | Cubre análisis, diseño, desarrollo, seguridad, testing y defensa del proyecto.                 |
| **Porcentaje** | 25% de la nota del periodo.                                                                    |

---

### Entregables Esperados

El proyecto se articula en cuatro entregables concretos que deben presentarse en su totalidad para obtener una calificación.

#### 1. Documento de Diseño de Solución

- Explicación detallada del problema, su justificación y su propósito.
- Propuesta de solución representada mediante:
    - Diagramas de flujo de los procesos principales.
    - Documentación de seguridad: Token based auth / Cookie based auth.
    - Diagrama Entidad-Relación (ERD) normalizado.
    - Matriz de pruebas completa.

#### 2. Documentación Interactiva

- Definición completa de endpoints en Swagger o Stoplight.
- Descripción de parámetros, respuestas y códigos HTTP.
- Ejemplos de request y response por cada ruta.

#### 3. Repositorio GitHub

- Código fuente limpio y bien estructurado.
- README detallado con instrucciones de levantamiento.
- Historial de commits organizado y descriptivo.

#### 4. Defensa en Vivo

- Demostración funcional de los flujos principales.
- Explicación clara de las decisiones de diseño.
- Respuesta fundamentada a preguntas técnicas.

---

### Rúbrica de Evaluación

La evaluación del proyecto está estructurada en cinco criterios independientes, cada uno con una ponderación específica.

| Ponderación | Criterio             | Descripción                                                 |
| ----------- | -------------------- | ----------------------------------------------------------- |
| 30%         | Análisis y Diseño    | Flujogramas, ERD y coherencia de la propuesta.              |
| 25%         | Desarrollo de API    | Endpoints RESTful, validaciones y flujos implementados.     |
| 15%         | Seguridad y Roles    | Sanctum, Policies, Gates y gestión de permisos.             |
| 15%         | Calidad y Testing    | Feature Tests y Unit Tests con Pest/PHPUnit.                |
| 15%         | Defensa del Proyecto | Demostración del proyecto y respuesta a preguntas técnicas. |

---

### Detalle de Rúbrica por Criterio

#### Criterio 1: Análisis y Diseño

> **Ponderación:** 30% · **Entregable:** Documento PDF + Swagger/Stoplight

Este criterio evalúa la capacidad del equipo para abstraer el problema planteado y representarlo mediante herramientas de modelado estándar. Un buen análisis y diseño es la base sobre la que se construye toda la solución técnica.

Se espera que los flujogramas cubran al menos los casos de uso más relevantes del sistema. El ERD debe reflejar una estructura relacional normalizada, sin redundancias innecesarias, con llaves foráneas correctamente definidas. Adicionalmente, los endpoints documentados en Swagger o Stoplight deben ser coherentes con los flujos modelados.

| Nivel      | Puntaje | Descripción                                                                                                             |
| ---------- | ------- | ----------------------------------------------------------------------------------------------------------------------- |
| Competente | 10–9    | 3+ flujogramas lógicos y ERD normalizado que cubre todos los casos de uso. Endpoints documentados en Swagger/Stoplight. |
| Intermedio | 8–7     | Presenta flujogramas, pero el ERD tiene errores menores en relaciones o atributos.                                      |
| Básico     | 6–5     | Documentación incompleta; los flujos no coinciden con la implementación real de la API.                                 |
| Deficiente | 4–0     | Ausencia de diagramas o falta total de coherencia con el problema planteado.                                            |

---

#### Criterio 2: Desarrollo de API

> **Ponderación:** 25% · **Entregable:** Stoplight / GitHub

El desarrollo de la API es el criterio de mayor peso en la evaluación, ya que representa el corazón técnico del proyecto. Se espera que los endpoints sigan los principios RESTful: uso correcto de verbos HTTP (GET, POST, PUT, PATCH, DELETE), rutas semánticas, respuestas con códigos de estado apropiados y mensajes estructurados de error. La organización del código debe aprovechar las herramientas nativas de Laravel: Resource Controllers, Form Requests para validación, y API Resources para la transformación de respuestas.

| Nivel      | Puntaje | Descripción                                                                                                                                                                                      |
| ---------- | ------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Competente | 10–9    | Endpoints siguen estándares RESTful completos. Uso correcto de Resource Controllers, Form Requests para validación y API Resources para respuestas. Documentación completa en Stoplight/Swagger. |
| Intermedio | 8–7     | La API funciona correctamente en los flujos principales, pero faltan algunos estándares REST o la documentación está incompleta en ciertos endpoints.                                            |
| Básico     | 6–5     | La API tiene errores de lógica en flujos secundarios. La documentación es básica o generada manualmente sin aprovechar las herramientas disponibles.                                             |
| Deficiente | 4–0     | La API no corre exitosamente o carece de validaciones de datos mediante Request Validation. No es posible demostrar su funcionamiento.                                                           |

---

#### Criterio 3: Seguridad y Roles

> **Ponderación:** 20% · **Entregable:** Demo + GitHub

Este criterio evalúa la implementación correcta de la capa de autenticación —ya sea basada en tokens o en cookies—, junto con el uso de Policies y Gates para controlar el acceso a recursos específicos según el rol o identidad del usuario autenticado.

La gestión de permisos debe estar alineada con la matriz de roles definida en el documento PDF, respetando una política de roles y permisos correctamente estructurada que garantice la atomicidad de los permisos asignados a un usuario.

| Nivel      | Puntaje | Descripción                                                                                                          |
| ---------- | ------- | -------------------------------------------------------------------------------------------------------------------- |
| Competente | 10–9    | Implementación completa de Sanctum con Policies/Gates que restringen el acceso según la matriz de permisos definida. |
| Intermedio | 8–7     | Usa Sanctum y Roles, pero existen brechas menores en la autorización de ciertos recursos o endpoints.                |
| Básico     | 6–5     | Implementa autenticación básica pero falla al aplicar correctamente la lógica de roles mediante Policies.            |
| Deficiente | 4–0     | No hay seguridad implementada o cualquier usuario puede acceder a cualquier recurso sin restricción.                 |

---

#### Criterio 4: Calidad y Testing

> **Ponderación:** 20% · **Entregable:** Demo + GitHub

Se espera que el estudiante implemente tanto Feature Tests como Unit Tests. Las herramientas esperadas son Pest o PHPUnit, ambas nativas del ecosistema Laravel.

Las pruebas no deben limitarse al "happy path". Es fundamental cubrir escenarios de error: validaciones fallidas (422), accesos no autorizados (401/403) y casos borde de la lógica de negocio.

| Nivel      | Puntaje | Descripción                                                                                                                  |
| ---------- | ------- | ---------------------------------------------------------------------------------------------------------------------------- |
| Competente | 10–9    | Cobertura de pruebas en flujos críticos (Feature Tests) y lógica de negocio (Unit Tests). Todos los tests pasan sin errores. |
| Intermedio | 8–7     | Presenta pruebas funcionales pero no cubren casos de error ni restricciones de seguridad.                                    |
| Básico     | 6–5     | Pruebas mínimas que solo validan el "camino feliz" (status 200) sin profundidad ni casos alternativos.                       |
| Deficiente | 4–0     | No incluye pruebas automatizadas o las pruebas existentes fallan al ejecutarse con `php artisan test`.                       |

---

#### Criterio 5: Defensa del Proyecto

> **Ponderación:** 15% · **Entregable:** Presentación oral en vivo

Se evaluarán tres dimensiones: la organización del repositorio GitHub (README profesional, estructura de carpetas clara, instrucciones de instalación funcionales), la demostración en vivo de los flujos principales de la API, y la capacidad de responder preguntas técnicas con seguridad y precisión.

Durante la defensa, se podrá solicitar que se demuestre cualquier aspecto del sistema: desde la ejecución de los tests hasta la explicación de una decisión de diseño específica en el código. El estudiante debe estar preparado para navegar el repositorio, ejecutar la aplicación en tiempo real y articular claramente por qué tomó determinadas decisiones técnicas. Un código que funciona pero que el estudiante no puede explicar será penalizado en este criterio.

| Nivel      | Puntaje | Descripción                                                                                                              |
| ---------- | ------- | ------------------------------------------------------------------------------------------------------------------------ |
| Competente | 10–9    | Repositorio organizado con README profesional, código limpio y defensa técnica fluida y segura ante todas las preguntas. |
| Intermedio | 8–7     | Repositorio aceptable. La defensa muestra dudas menores en la explicación técnica de algunas decisiones.                 |
| Básico     | 6–5     | Repositorio desordenado o sin instrucciones claras. La defensa es débil, poco clara o el estudiante depende de notas.    |
| Deficiente | 4–0     | No entrega repositorio o es incapaz de explicar el funcionamiento del código durante la defensa oral.                    |

---

### Instrucciones de Entrega

La documentación completa (PDF, enlace al repositorio y enlace a la documentación interactiva) deberá subirse a la plataforma **Moodle** a más tardar el **miércoles 8 de abril a las 11:59 p. m.**

> ⚠️ **Penalización:** El cumplimiento de este plazo es obligatorio. Cualquier equipo que no haya cargado su documentación en el tiempo estipulado será penalizado directamente en la nota de la defensa oral.

---

### Logística de la Defensa en Vivo

Las presentaciones se llevarán a cabo según el horario asignado previamente por el docente. Se aplicarán estrictamente las siguientes reglas:

- **Duración:** Cada equipo contará con un máximo de **20 minutos** para su presentación y demostración funcional. El tiempo será cronometrado.
- **Puntualidad:** Los integrantes del grupo deben estar presentes y listos **15 minutos antes** de su hora asignada.
- **Preparación:** Es responsabilidad del equipo contar con todo lo necesario (computadoras, adaptadores, cargadores, acceso al repositorio y base de datos local/remota lista).
- **Incomparecencia:** Si el equipo no está preparado en el salón a la hora exacta designada o se le agota el tiempo establecido para defender, perderá automáticamente la oportunidad de presentar el proyecto, lo que resultará en una **nota de cero** en ese criterio.

## Bitácora de Decisiones y Contexto del Proyecto

Este documento sirve como registro de decisiones importantes relacionadas con la arquitectura, lógica de negocio y funcionamiento general del proyecto, especialmente concebidas durante discusiones de desarrollo.

### Lógica de Seeders y Factories (Testing y Desarrollo)

1. **Fechas: Relativas vs Fijas**:
    - Por defecto, se utilizan **fechas relativas** (ej. `now()`, `now()->addDays(7)`) en las Factories para asegurar que al levantar el entorno (ej. `migrate:fresh --seed`), la información generada represente un estado "activo" o "próximo" adecuado para interactuar en el momento de la ejecución.
    - Las **fechas fijas** se reservan estrictamente para pruebas unitarias donde se necesite precisión absoluta (ej. comprobación de caducidad o control de reportes periódicos).

2. **Categorías**:
    - Para ambientes de prueba, los nombres de categoría son generados dinámicamente utilizando `faker` (en inglés), contemplando que algunas categorías puedan estar formadas por múltiples palabras (ej. `fake()->words(2, true)`).

3. **Consistencia y Cantidad de Miembros en Equipos (Teams)**:
    - Un equipo (`Team`) se inicializa asumiendo que el _único_ miembro es el líder (`admin_id`). Por lo tanto, su valor predeterminado para `total_members` es 1.
    - El tope de `max_members` se mantiene en el valor dictaminado por defecto (5).
    - _Regla de Consistencia Escrita_: La propiedad `total_members` NUNCA debe ser generada de manera completamente aleatoria. Debe reflejar y escalarse con base a las relaciones reales de los registros en `User` que pertenezcan a este `Team`.

4. **Roles Obligatorios en Relaciones Clave**:
    - En la tabla `teams`, la referencia `admin_id` está reservada exclusivamente para un usuario que ya cuente con el rol de **`lider`**.
    - En la tabla `competitions`, la referencia `admin_id` está reservada exclusivamente para un usuario que ya cuente con el rol de **`organizador`**.

5. **Reglas de Entregas (Handovers)**:
    - Estado de completitud: "Entregada" (`true`) o "Incompleta" (`false`).
    - `is_delivered = true` es una afirmación válida **ÚNICAMENTE** si la entrega posee un archivo adjunto que no sea nulo (`attachment != null`). Por ende, los Handover default sin archivo constan como falso.
    - Una entrega (`Handover`) solo puede tener una puntuación calificada (`score`) SI Y SOLO SI ha sido efectivamente entregada o enviada (`is_delivered == true`). En caso contrario, el puntaje debe constar siempre como `null`.

6. **Estructura Arquitectónica de Seeders**:
    - Para mantener un código limpio y predecible, se respeta estrictamente la regla de **"Un Seeder por Modelo"**. Por ejemplo, en lugar de crear seeders independientes para cada rol de usuario, se utiliza un único orquestador (`UserSeeder`) para poblar todas las variaciones de usuarios que el proyecto demanda.

### 7. Pruebas de Endpoints de Equipos (Teams) con cURL

A continuación se presentan peticiones robustas vía `curl` para testear el ciclo CRUD completo de Equipos, así como Soft Deletes.
_(Nota: Sustituir `<ID>` por el ID correspondiente de la BD)_

**1. Crear un Equipo (POST)**

```bash
curl -X POST http://localhost:8000/api/v1/teams \
-H "Accept: application/json" \
-H "Content-Type: application/json" \
-H "Authorization: Bearer <TU_TOKEN_AQUI>" \
-d '{
    "name": "Equipo Destructor",
    "admin_id": 3,
    "total_members": 1,
    "max_members": 5
}'
```

**2. Listar Equipos Activos (GET)**

```bash
curl -X GET http://localhost:8000/api/v1/teams \
-H "Accept: application/json" \
-H "Authorization: Bearer <TU_TOKEN_AQUI>"
```

**3. Obtener un Equipo Específico (GET)**

```bash
curl -X GET http://localhost:8000/api/v1/teams/<ID> \
-H "Accept: application/json" \
-H "Authorization: Bearer <TU_TOKEN_AQUI>"
```

**4. Actualizar un Equipo (PUT)**

```bash
curl -X PUT http://localhost:8000/api/v1/teams/<ID> \
-H "Accept: application/json" \
-H "Content-Type: application/json" \
-H "Authorization: Bearer <TU_TOKEN_AQUI>" \
-d '{
    "name": "Equipo Destructor V2",
    "total_members": 2,
    "max_members": 6
}'
```

**5. Eliminar lógicamente (Soft Delete) un Equipo (DELETE)**

```bash
curl -X DELETE http://localhost:8000/api/v1/teams/<ID> \
-H "Accept: application/json" \
-H "Authorization: Bearer <TU_TOKEN_AQUI>"
```

**6. Listar Equipos Eliminados Lógicamente (GET)**

```bash
curl -X GET http://localhost:8000/api/v1/teams/deleted \
-H "Accept: application/json" \
-H "Authorization: Bearer <TU_TOKEN_AQUI>"
```

**7. Restaurar un Equipo Eliminado Lógicamente (PATCH)**

```bash
curl -X PATCH http://localhost:8000/api/v1/teams/<ID>/restore \
-H "Accept: application/json" \
-H "Authorization: Bearer <TU_TOKEN_AQUI>"
```

### 8. Convenciones de Enrutamiento y Estilo

1. **Definición Explícita de Endpoints**:
    - Se prefiere declarar todos los endpoints explícitamente (GET, POST, PATCH, PUT, DELETE, etc.) en lugar de agruparlos de manera opaca mediante métodos como `Route::apiResource`. Esto agiliza la lectura rápida, búsqueda y la visualización de la superficie de la API directamente desde `routes/api.php`.
2. **Importación de Clases**:
    - Es obligatorio importar los elementos completos del sistema (Controladores, Requests, Modelos) utilizando la sentencia `use` en la parte superior del archivo.
    - En el cuerpo del código, siempre se debe utilizar la versión acortada (por ejemplo, `TeamController::class`) en lugar de escupir en línea el namespace absoluto (`App\Http\Controllers\TeamController::class`). Esto prioriza un código limpio y legible a simple vista.

## Commands

```bash
# Initial setup
composer run setup

# Start development server (runs server, queue, and vite concurrently)
composer run dev

# Run all tests
composer run test
# or
php artisan test

# Run specific test file
php artisan test --filter=CategoryControllerTest

# Run specific test method
php artisan test --filter=test_example

# Code style (Laravel Pint)
vendor/bin/pint

# Clear cache
php artisan config:clear
php artisan cache:clear
```

## Architecture

### Models and Relationships

```
User ─┬─ belongsTo ─ Team
       ├─ hasOne ───── Team (as admin)
       └─ hasMany ─── Competition (as admin)

Team ─┬─ belongsTo ───── User (admin)
      ├─ hasMany ────── User (members)
      ├─ belongsToMany ─ Competition
      └─ hasMany ────── Handover

Competition ─┬─ belongsTo ─ Category
             ├─ belongsTo ─ User (admin)
             ├─ belongsToMany ─ Team
             └─ hasMany ──── Module

Module ─┬─ belongsTo ─ Competition
        └─ hasMany ─── Handover

Handover ─┬─ belongsTo ─ Module
          └─ belongsTo ─ Team

Category ─ hasMany ─ Competition
```

### Key Patterns

- **Soft Deletes**: Team, Competition, Category, Module, Handover use `SoftDeletes` trait. Controllers implement `deleted()` and `restore()` endpoints for soft-deleted records.
- **API Resources**: All responses use `*Resource` classes (e.g., `CategoryResource`, `TeamResource`) for consistent JSON transformation.
- **Form Requests**: Validation logic in `Store*Request` and `Update*Request` classes.
- **Policies**: Authorization via Policy classes using spatie permissions (e.g., `$user->can('teams.view')`).
- **Route Model Binding**: Routes use `{model}` parameter with `->missing()` handlers for 404 responses.

### API Structure

All routes prefixed with `/v1/`. Authentication endpoints are public; all others require `auth:sanctum` middleware.

```
POST   /v1/auth/login          # Public
POST   /v1/auth/logout         # Auth required
GET    /v1/auth/profile       # Auth required

GET    /v1/categories         # CRUD endpoints
POST   /v1/categories
GET    /v1/categories/{category}
PUT    /v1/categories/{category}
DELETE /v1/categories/{category}

# Same pattern for: teams, competitions, modules, handovers
# Teams additionally have: GET /teams/deleted, PATCH /teams/{id}/restore
```

### Database

Default uses SQLite (`:memory:` for testing). Migrations follow Laravel naming: `create_{table}_table.php` and `add_attributes_to_{model}.php`.

## Testing

PestPHP is configured. Tests use in-memory SQLite database. Feature tests extend `Tests\TestCase` via `pest()->extend()` in `tests/Pest.php`.
