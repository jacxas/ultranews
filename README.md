# ⚡ UltraNews v5.0.0

**Portal de noticias de videos virales de las redes sociales**, construido con Laravel 13.

Cubre los videos que explotan en **YouTube, TikTok, Instagram, X, Vimeo y Facebook**: los embebe en cada noticia, mide su engagement y los ordena con un algoritmo de tendencias al estilo Hacker News. Incluye sitio público, panel de administración y API REST.

> 📋 **Historia del proyecto:** la v4.2.0 fue solo una especificación (el repo nunca llegó a tener código — ver [`AUDITORIA.md`](AUDITORIA.md)). La **v5.0.0 es la reconstrucción completa y funcional** sobre Laravel 13, retomando y ampliando aquella spec con el foco original: noticias de videos virales.

## ✨ Características

- 📰 **Noticias con video embebido**: pegás la URL de YouTube/TikTok/Instagram/X/Vimeo/Facebook y el sistema detecta plataforma, ID, embed y miniatura automáticamente.
- 🔥 **Algoritmo de tendencias**: `score = engagement / (edad_horas + 2) ^ gravedad`, con engagement ponderado (vistas, likes, compartidos, comentarios).
- 🤖 **Ingestión automática**: `php artisan ultranews:fetch-viral` importa los videos más populares de YouTube (Data API v3) como noticias, programable con el scheduler.
- 🗂️ **Categorías, etiquetas y búsqueda** con slugs amigables y SEO (meta título/descripción, Open Graph).
- 🔐 **Panel de administración** (Blade, sin necesidad de compilar assets): CRUD de noticias y categorías, borradores/publicación programada, destacadas, papelera con restauración.
- 🔌 **API REST** con filtros, orden por tendencias y autenticación por token personal (sin dependencias extra).
- 👁️ **Contador de vistas** anti-spam (1 por sesión en la web + endpoint público en la API).
- 📅 **Publicación programada**: las noticias con fecha futura se ocultan hasta su momento.
- 🧪 **Suite de tests** (PHPUnit 12): unitarios de los servicios + feature tests de web, API, panel y comandos.

## 🧱 Stack

| Componente | Versión |
|---|---|
| PHP | 8.3+ |
| Laravel Framework | 13.x |
| Base de datos | SQLite (dev, cero configuración) / MySQL 8 (producción) |
| Frontend | Blade + CSS propio (sin build step obligatorio) |
| Tests | PHPUnit 12 |

## 🚀 Instalación

```bash
git clone https://github.com/jacxas/ultranews.git
cd ultranews

composer install
cp .env.example .env
php artisan key:generate

# SQLite (por defecto): crear el archivo de base de datos
touch database/database.sqlite

# Migrar + cargar datos demo (videos virales reales de todas las épocas)
php artisan migrate --seed

php artisan serve
```

Listo: http://localhost:8000

### 🔑 Credenciales demo

| | |
|---|---|
| Panel | http://localhost:8000/login |
| Usuario | `admin@ultranews.test` |
| Contraseña | `password` |

### MySQL (producción)

En `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ultranews
DB_USERNAME=root
DB_PASSWORD=secret
```

## 🤖 Importar videos virales de YouTube

1. Creá una clave en [Google Cloud Console](https://console.cloud.google.com/apis/credentials) habilitando **YouTube Data API v3** (gratis).
2. Configurá en `.env`:
   ```env
   YOUTUBE_API_KEY=tu-clave
   ULTRANEWS_REGION=AR
   ```
3. Ejecutá:
   ```bash
   php artisan ultranews:fetch-viral                  # top 10 de la región
   php artisan ultranews:fetch-viral --limit=25 --region=MX
   php artisan ultranews:fetch-viral --dry-run        # ver qué importaría, sin guardar
   ```

El comando deduplica por `video_id` (re-ejecutarlo actualiza métricas sin duplicar noticias) y queda programado **cada hora** vía `routes/console.php` (activar con el cron del scheduler: `* * * * * php artisan schedule:run`).

## 🌐 Rutas del sitio

| Método | URI | Descripción |
|---|---|---|
| GET | `/` | Portada: destacada, tendencias, últimas, plataformas |
| GET | `/tendencias` | Ranking completo por trending score |
| GET | `/buscar?q=` | Búsqueda por título/bajada/cuerpo |
| GET | `/plataforma/{platform}` | Noticias por plataforma (youtube, tiktok, instagram, x, vimeo, facebook) |
| GET | `/categoria/{slug}` | Noticias de una categoría |
| GET | `/etiqueta/{slug}` | Noticias por etiqueta |
| GET | `/noticia/{slug}` | Detalle con video embebido + relacionadas |
| GET/POST | `/login`, `/logout` | Autenticación del panel |
| * | `/admin/**` | Panel (requiere sesión + `is_admin`) |
| GET | `/up` | Healthcheck |

## 🔌 API REST

Lectura pública; escritura con token personal (`Authorization: Bearer <token>` + usuario admin).

### Generar un token

```bash
php artisan ultranews:issue-token admin@ultranews.test   # muestra el token una única vez
php artisan ultranews:issue-token admin@ultranews.test --revoke
```

### Endpoints

| Método | Endpoint | Auth | Descripción |
|---|---|---|---|
| GET | `/api/articles` | — | Listado paginado. Filtros: `category`, `tag`, `platform`, `q`, `featured`. Orden: `sort=trending\|recent\|views`. `per_page` máx. 50 |
| GET | `/api/articles/{slug}` | — | Detalle (incluye `video.embed_url`, stats, tags) |
| POST | `/api/articles/{slug}/view` | — | Incrementa el contador de vistas |
| GET | `/api/trending?limit=&platform=` | — | Top tendencias |
| GET | `/api/categories` · `/api/categories/{slug}` | — | Categorías con conteo |
| GET | `/api/tags` | — | Etiquetas con conteo |
| POST | `/api/articles` | 🔑 | Crear noticia |
| PUT/PATCH | `/api/articles/{slug}` | 🔑 | Actualizar (campos ausentes = sin cambios; `tags` = lista separada por comas) |
| DELETE | `/api/articles/{slug}` | 🔑 | Soft delete |

### Ejemplos

```bash
# Tendencias
curl http://localhost:8000/api/trending?limit=5

# Crear una noticia
curl -X POST http://localhost:8000/api/articles \
  -H "Authorization: Bearer TU_TOKEN" \
  -H "Accept: application/json" \
  -d "title=Nuevo challenge explosivo en TikTok" \
  -d "video_url=https://www.tiktok.com/@usuario/video/1234567890123456789" \
  -d "status=published" \
  -d "tags=viral,tiktok,challenge"
```

## 🛠️ Comandos Artisan

| Comando | Descripción |
|---|---|
| `ultranews:fetch-viral` | Importa los videos más populares de YouTube |
| `ultranews:refresh-trending` | Recalcula los scores de tendencias (decaen con el tiempo) |
| `ultranews:issue-token {email}` | Genera/revoca tokens de API |

## 📁 Estructura

```
ultranews/
├── app/
│   ├── Console/Commands/        # fetch-viral, refresh-trending, issue-token
│   ├── Http/
│   │   ├── Controllers/         # Web/, Api/, Admin/, Auth/
│   │   ├── Middleware/          # admin + api.token
│   │   ├── Requests/            # ArticleRequest (web + API)
│   │   └── Resources/           # Article, Category, Tag
│   ├── Models/                  # Article, Category, Tag, User
│   ├── Services/                # VideoEmbedService, TrendingService
│   └── Support/                 # NumberFormat
├── config/ultranews.php         # tendencias, ingestión, plataformas
├── database/
│   ├── factories/               # Article, Category, Tag, User
│   ├── migrations/              # users+, categories, tags, articles, article_tag
│   └── seeders/                 # datos demo con videos virales reales
├── public/css/ultranews.css     # tema oscuro (sin build step)
├── resources/views/             # layouts, partials, artículos, auth, admin
├── routes/                      # web.php, api.php, console.php (scheduler)
└── tests/                       # Unit + Feature (web, api, admin, consola)
```

## 🧪 Tests

```bash
composer test        # o: php artisan test
```

Cubren: parser de URLs de video (16 casos), algoritmo de tendencias, portada, detalle con embed y contador de vistas, categorías/etiquetas/búsqueda/plataformas, login/logout y permisos, CRUD del panel con papelera, API completa con tokens y comandos de ingestión (con `Http::fake`).

## 📈 Algoritmo de tendencias

```
engagement = views·1 + likes·2 + shares·3 + comments·4
score      = engagement / (edad_horas + 2) ^ gravedad     # gravedad default: 1.6
```

El score se recalcula en cada guardado y mediante `ultranews:refresh-trending` (programado cada 15 min). Pesos y gravedad configurables en `config/ultranews.php`.

## 🚢 Despliegue

- **Requisitos**: PHP 8.3+ con extensiones estándar de Laravel, base de datos (MySQL 8 recomendado), cron para el scheduler.
- `.env` de producción: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` real, credenciales de BD y `YOUTUBE_API_KEY`.
- Post-deploy: `composer install --no-dev -o && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan storage:link`.
- El frontend no requiere `npm run build` (CSS estático en `public/css/`). Vite/Tailwind quedan disponibles opcionalmente.

## 📄 Licencia

MIT — ver [LICENSE](LICENSE).

## 🙋 Autor

**jacxas** (javier Castro)

---

*Última actualización: 2026-10-25 · v5.0.0 (reconstrucción completa sobre Laravel 13)*
