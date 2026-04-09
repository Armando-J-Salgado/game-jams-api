# Game Jams API

API REST desarrollada con Laravel 13 para gestionar competencias tipo game jam, equipos de trabajo, módulos de evaluación y entregas. Su objetivo es centralizar la lógica de negocio y exponer un backend seguro para consumidores web, móviles o integraciones de terceros.

## Descripción del proyecto

Game Jams API resuelve la necesidad de administrar el ciclo operativo de una competencia de desarrollo de videojuegos: creación de competencias, gestión de categorías, inscripción de equipos, seguimiento de miembros y control de entregas por módulo.

El sistema está pensado para ser consumido por:

- Frontends web administrativos y para participantes.
- Integraciones externas (por ejemplo, dashboards o automatizaciones).

### Alcance

El proyecto cubre:

- Gestión de usuarios, roles y permisos.
- Gestión de categorías, competencias, equipos, módulos y entregas.
- Acciones de negocio clave (inscribir/retirar equipo, gestión de miembros, envío de entregas).
- Validación de datos, autorización por políticas y respuestas JSON consistentes.

El proyecto no cubre actualmente:

- Interfaz de usuario final (solo backend/API).
- Orquestación avanzada de despliegue (CI/CD).

### Estado de madurez

Proyecto en desarrollo activo, funcional para flujos principales de negocio y apto para demostración académica/técnica. Requiere endurecimiento adicional para operación productiva de alto tráfico.

## Stack tecnológico

- Laravel 13: framework principal para la arquitectura de API, routing, middleware, validaciones y capa ORM.
- PHP 8.3+: lenguaje y runtime requeridos por el framework y dependencias del proyecto.
- Laravel Herd: entorno local recomendado para servir el proyecto de forma rápida y estable en macOS/Windows.
- Laravel Sanctum: autenticación basada en token para proteger endpoints API.
- Spatie Laravel Permission: gestión de roles y permisos finos sobre recursos.
- SQLite (por defecto) / MySQL (opcional): persistencia local simple por defecto con opción de migrar a motor relacional dedicado.
- Pest + PHPUnit: framework de testing para pruebas feature y unitarias.

## Requisitos del sistema

Antes de instalar, valida lo siguiente:

- PHP 8.3 o superior.
- Composer 2.x.
- Node.js 20+ y npm (para assets/comandos de frontend cuando aplique).
- Extensiones PHP comunes para Laravel:
	- OpenSSL
	- PDO
	- Mbstring
	- Tokenizer
	- XML
	- Ctype
	- JSON
	- BCMath
	- Fileinfo
- Base de datos:
	- SQLite (incluida por defecto en la configuración local del proyecto), o
	- MySQL/MariaDB si se cambia DB_CONNECTION.
- Laravel Herd instalado y operativo para ejecución local recomendada.

Compatibilidad esperada:

- Windows (incluyendo setup con Herd).
- macOS (setup con Herd).
- Linux (sin Herd, usando stack equivalente con PHP + servidor local).

## Instalación

### Flujo recomendado desde cero

1. Clona el repositorio.
2. Instala dependencias PHP con Composer.
3. Crea y ajusta variables de entorno locales.
4. Genera APP_KEY.
5. Ejecuta migraciones y seeders.
6. Instala dependencias JS si vas a usar el flujo de desarrollo completo.
7. Inicia la API con Herd o con el comando de desarrollo del proyecto.

### Comandos útiles

```bash
composer run setup
```

Este comando automatiza instalación inicial básica (dependencias, .env, key, migraciones y build de assets).

Para entorno de desarrollo con procesos concurrentes:

```bash
composer run dev
```

## Configuración del entorno

La configuración se centraliza en el archivo .env. Las variables críticas son:

- APP_NAME, APP_ENV, APP_DEBUG, APP_URL: identidad y comportamiento base de la app.
- DB_CONNECTION, DB_DATABASE, DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD: conexión de base de datos.
- QUEUE_CONNECTION: cola de trabajos (por defecto database).
- CACHE_STORE y SESSION_DRIVER: estrategia de cache y sesión.

### Valores locales esperados con Herd

- APP_ENV=local
- APP_DEBUG=true
- APP_URL apuntando al dominio local de Herd (por ejemplo, http://game-jams-api.test)
- DB_CONNECTION=sqlite para onboarding rápido

### Base de datos

Por defecto el proyecto usa SQLite. Para entorno local simple:

- Mantén DB_CONNECTION=sqlite.
- Verifica que exista el archivo database/database.sqlite (si no existe, créalo).

Si usarás MySQL/MariaDB:

- Cambia DB_CONNECTION.
- Configura credenciales válidas.
- Ejecuta migraciones nuevamente.

## Ejecución local con Laravel Herd

Con Herd instalado, asegúrate de que la carpeta del proyecto esté enlazada en Herd y que el dominio local esté resuelto, normalmente:

- http://game-jams-api.test

### Verificación de arranque correcto

- El dominio local responde sin error DNS.
- Las rutas de API responden bajo prefijo /api/v1.
- Puedes autenticarte por POST /api/v1/auth/login y obtener token.

### Errores comunes en Herd

- Dominio no resuelve:
	- Verifica que el proyecto esté marcado como sitio en Herd.
- Error de conexión DB:
	- Revisa DB_CONNECTION y credenciales en .env.
- Error de clave de aplicación:
	- Ejecuta php artisan key:generate.
- Error de tablas inexistentes:
	- Ejecuta php artisan migrate --seed.

## Estructura general del proyecto

La aplicación sigue una organización clásica de Laravel enfocada en API:

- app/Http/Controllers: orquestación de casos de uso HTTP.
- app/Http/Requests: validaciones de entrada por endpoint (Form Requests).
- app/Http/Resources: transformación de salida JSON (API Resources).
- app/Models: entidades de dominio y relaciones Eloquent.
- app/Policies: reglas de autorización por recurso.
- database/migrations: evolución del esquema de datos.
- database/seeders y database/factories: datos de prueba/arranque.
- routes/api.php: definición explícita de endpoints API versionados.
- tests/Feature y tests/Unit: pruebas de comportamiento y lógica aislada.

## Arquitectura y decisiones técnicas

Decisiones relevantes implementadas:

- Endpoints explícitos en rutas API en lugar de recursos implícitos, para facilitar lectura y control fino.
- Uso de Form Requests para separar validación de la lógica del controlador.
- Uso de API Resources para estandarizar payloads de respuesta.
- Políticas y permisos desacoplados de controladores mediante Gate/Policy y Spatie Permission.
- Soft deletes en entidades clave para permitir recuperación de datos sin pérdida física inmediata.

Esta combinación prioriza mantenibilidad, trazabilidad de reglas de negocio y evolución incremental.

## Diseño conceptual de la API

La API sigue estilo REST con versionado por prefijo /api/v1. La convención principal es:

- Recursos con rutas semánticas en plural.
- Operaciones CRUD y acciones de negocio separadas.
- Respuestas JSON.
- Uso de códigos HTTP acordes al resultado (200, 201, 204, 401, 403, 404, 422, etc.).

La documentación formal de endpoints se mantiene en Stoplight para consumo externo y gobernanza contractual.

Enlace directo a la documentación:

- [Game Jam API](https://2-many-bits-esen.stoplight.io/studio/game-jam-api:?source=3yGSi2C4kNzHH8_P8DH5x&symbol=%252Fp%252Fopenapi-2.yaml%252Fpaths%252F%7E1api%7E1v1%7E1users%7E1%257Buser_id%257D)

## Autenticación y autorización

### Autenticación

- Login por credenciales en /api/v1/auth/login.
- Emisión de token personal con Laravel Sanctum.
- Endpoints protegidos bajo middleware auth:sanctum.
- Logout invalidando tokens activos del usuario.

### Autorización

- Modelo RBAC con roles como administrador, organizador, líder y participante.
- Permisos granulares por recurso/acción (por ejemplo teams.view, competitions.create, modules.update).
- Políticas por entidad para validar acceso a operaciones de lectura/escritura.

## Validaciones y manejo de errores

El proyecto usa Form Requests para validar payloads de entrada en operaciones de creación y actualización.

Convenciones generales:

- 422 para errores de validación de datos.
- 401 para solicitudes no autenticadas.
- 403 para solicitudes autenticadas sin permiso.
- 404 para recursos inexistentes (incluyendo manejo explícito en route model binding).

Las respuestas de error se devuelven en JSON y deben consumirse por cliente con manejo consistente de status code + cuerpo.

## Persistencia de datos

La persistencia se gestiona con Eloquent ORM y migraciones versionadas.

Puntos clave:

- Base local por defecto en SQLite.
- Migraciones para estructura y cambios incrementales del esquema.
- Seeders para poblar roles, permisos, usuarios y catálogos base.
- Factories para generación de datos de prueba.
- Soft deletes implementados en múltiples modelos de negocio (equipos, competencias, categorías, módulos y entregas).

## Testing

El proyecto utiliza Pest sobre PHPUnit.

Tipos de pruebas presentes:

- Feature tests: validan comportamiento de endpoints y reglas de negocio sobre flujo HTTP.
- Unit tests: validan lógica aislada.

Ejecución:

```bash
composer run test
```

o

```bash
php artisan test
```

Se espera que toda contribución relevante incluya o actualice pruebas; testing forma parte obligatoria del flujo de calidad.

## Mocks y herramientas externas

Actualmente el proyecto se apoya en factories/seeders para simular datos de dominio en desarrollo y pruebas.

Limitaciones:

- Estos datos simulados no reemplazan pruebas de integración con servicios reales externos.
- Ciertos comportamientos de producción (carga, latencia, infraestructura) no quedan cubiertos solo con datos mock.

## Normas de desarrollo y contribución

Expectativas generales:

- Usar Form Requests para validación y Resources para respuestas.
- Respetar separación de responsabilidades por capas.
- Escribir pruebas para nuevos flujos o correcciones críticas.
- Usar commits descriptivos y atómicos.

Flujo recomendado:

1. Crear rama de trabajo.
2. Implementar cambio con pruebas.
3. Ejecutar suite de tests y revisión de estilo.
4. Abrir PR con contexto funcional y técnico.

## Seguridad

Prácticas implementadas:

- Autenticación con tokens mediante Sanctum.
- Autorización por roles/permisos con políticas.
- Validación de entrada con Form Requests.
- Protección de endpoints sensibles con middleware auth:sanctum.

Buenas prácticas operativas:

- No exponer .env ni credenciales.
- Rotar secretos y tokens periódicamente.
- Aplicar principio de mínimo privilegio por rol.

Limitaciones conocidas:

- Requiere auditoría periódica de permisos y políticas para evitar brechas por crecimiento funcional.

## Limitaciones actuales y roadmap

Limitaciones:

- Aún sin guía de despliegue automatizada completa.

Mejoras futuras sugeridas:

1. Endurecer observabilidad y trazabilidad de errores.
2. Ampliar cobertura de pruebas negativas y de autorización.
3. Definir pipeline CI/CD con controles de calidad automáticos.

## Mantenimiento y soporte

Mantenido por el equipo responsable de Game Jams API.

Para reportar problemas o solicitar soporte:

- Abre un issue en el repositorio con contexto reproducible.
- Incluye pasos, resultado esperado, resultado actual y evidencia (logs/respuestas).
