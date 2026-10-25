# Auditoría Completa — UltraNews (jacxas/ultranews)

**Fecha de la auditoría:** 2026-10-25
**Repo:** https://github.com/jacxas/ultranews
**Rama auditada:** `main` @ `670c383`
**Metodología:** inspección del working tree, historial git completo (unshallowed), API de GitHub (branches, tags, forks, issues, PRs, eventos, repos del usuario) y verificación de dependencias.

---

## 1. Resumen ejecutivo

> **El repositorio nunca contuvo código de aplicación.** Es un repo de **documentación/especificación** (3 archivos) que describe un CMS de noticias Laravel basado en Botble, pero el código real jamás fue commiteado. Además, el `composer.json` tal como está **no es instalable** (declara `botble/cms ^10.0`, versión que no existe). Por último, en ninguna parte del repo hay mención a "videos virales" o "redes sociales": esa funcionalidad o vivió en otro proyecto/máquina, o nunca llegó a documentarse aquí.

Nivel de riesgo general: **no hay nada que "recuperar" en este repo**, pero sí hay una spec clara sobre la cual reconstruir.

---

## 2. Inventario del estado actual

El working tree completo (rama `main`) contiene exactamente 3 archivos:

| Archivo | Tamaño | Líneas | Qué es |
|---|---|---|---|
| `README.md` | 3.042 B | 166 | Documentación del proyecto *intended* (instalación, estructura, API, troubleshooting) |
| `composer.json` | 1.682 B | 62 | Manifiesto PHP: Laravel 10 + Botble CMS |
| `.env.example` | 1.065 B | 58 | Variables de entorno estándar de Laravel 10 (BD MySQL, mail, Pusher, AWS, Redis) |

**Ausencias críticas** (archivos que un proyecto Laravel real tendría):

- ❌ `artisan`, `app/`, `bootstrap/`, `config/`, `database/`, `resources/`, `routes/`, `storage/`, `tests/`, `public/`
- ❌ `composer.lock` (sin lockfile no hay forma de saber qué se instaló alguna vez)
- ❌ `.gitignore`, `LICENSE` (el composer.json declara MIT pero no existe el archivo)
- ❌ Cualquier código de ingestión de videos / APIs sociales (YouTube, TikTok, etc.)

Dato corroborante: GitHub reporta `size: 5` KB y `language: null` (no detecta ningún lenguaje porque no hay código).

---

## 3. Historial git completo

El clone local era *shallow* (profundidad 1). Se descargó el historial completo: **3 commits en total**, todos en `main`. No hay tags, ni otras ramas remotas, ni forks, ni issues, ni PRs, ni eventos de actividad.

| # | SHA | Fecha | Mensaje | Contenido |
|---|---|---|---|---|
| 1 | `b83c016` | 2026-04-16 03:56 UTC | `Initial commit` | Solo `README.md` de 2 líneas (stub: "UltraNews v4.2.0 - Laravel CMS based on Botble") |
| 2 | `112f812` | 2026-04-16 05:19 UTC | `chore: Inicializar estructura de UltraNews CMS` | Agrega `.env.example` + `composer.json` y expande el README a la documentación completa |
| 3 | `670c383` | 2026-09-13 20:05 -0300 | `docs: marca de ultima actualizacion 2026-09-13` | Solo agrega 4 líneas al README ("Última actualización: 2026-09-13") |

**Timeline del repo en GitHub:**

- **2026-02-16** — Se crea el repo en GitHub (vacío).
- **2026-04-16** — Los dos primeros commits (stub + documentación inicial), ~1.5 h de diferencia entre sí.
- **2026-09-13** — Único cambio posterior: la marca de "última actualización". **5 meses sin actividad de código.**
- **2026-10-25** — Esta auditoría.

Autor de todos los commits: `javier Castro <jacxas@gmail.com>`.

Conclusión del historial: **no hay ningún commit borrado ni código eliminado** — simplemente nunca se subió. No existe un estado anterior más rico del que recuperar archivos.

---

## 4. Qué se suponía que era el proyecto (según la documentación)

El README describe la *intención*:

- **UltraNews v4.2.0** — CMS de noticias basado en **Laravel 10 + Botble CMS**.
- **Stack declarado:** PHP 8.1+, Laravel 10+, MySQL 8.0+, Composer.
- **Funcionalidades prometidas:** gestión de artículos/noticias, categorías y etiquetas, usuarios y permisos, SEO, API REST (`GET/POST/PUT/DELETE /api/articles`, `GET /api/categories`), panel de administración, diseño responsive.
- **Estructura prometida:** la estándar de Laravel (`app/`, `database/`, `resources/`, `routes/`, `storage/`, `tests/`, `config/`) — **ninguna existe**.
- **Despliegue documentado:** Vercel (con PHP), Railway, Heroku — sin archivos de configuración para ninguno (`vercel.json`, `Procfile`, etc. no existen).

### ⚠️ Discrepancia con tu recuerdo

Recordás el proyecto como **"noticias de videos virales en las redes sociales"**. En este repo **no hay ninguna mención** a videos, virales, TikTok, YouTube, Instagram, Reels ni redes sociales — ni en el README, ni en el `.env.example` (que no tiene claves de ninguna API de video/social), ni en el composer.json.

Posibles explicaciones (revisé tus 38 repos públicos en GitHub):

1. La funcionalidad viral/social era un **plan** que nunca llegó a documentarse ni implementarse aquí.
2. El proyecto real vivió **fuera de git** (en tu máquina local u otro hosting). El nombre **"v4.2.0"** es una pista fuerte: sugiere que existieron v1–v4.1 en algún lugar que nunca se pusheó.
3. Confusión con otro de tus repos. Los candidatos más cercanos por temática:
   - **`nexus-shorts-ai`** (TypeScript, jul 2026): *"TikTok, YouTube Shorts and Reels automated with Generative AI, virtual influencers, and virality optimization engines"* — es el más parecido a "videos virales en redes sociales".
   - **`instadown`** (JavaScript, mar 2026): sin descripción, nombre sugiere descargas de Instagram.
   - **`NPDHM`** (TypeScript): "SERIES Y PELICULAS".

---

## 5. Hallazgos técnicos detallados

### F1 — El repo es solo documentación (criticidad: alta)
Como se detalla arriba: 0 líneas de código de aplicación en toda la historia. El README actúa como spec aspiracional.

### F2 — `composer.json` no instalable (criticidad: alta)
```json
"botble/cms": "^10.0"
```
**No existe Botble CMS 10.x en Packagist.** La versión actual real es **7.6.x** (7.6.12, octubre 2026), que corre sobre **Laravel 13 / PHP 8.3+**. Un `composer install` con este manifiesto **falla** con "could not be found in any version". Es decir: este composer.json nunca fue validado contra Packagist.

### F3 — Stack desactualizado / EOL (criticidad: media)
- **Laravel 10** quedó sin soporte de seguridad en **febrero 2025**. Hoy (oct 2026) la línea soportada es Laravel 12/13.
- **PHP ^8.1**: correcto para Laravel 10, pero viejo; Botble 7.6 exige PHP 8.3+.
- El `.env.example` es el genérico de Laravel 10: le faltan variables que Botble genera tras su instalación (`CMS_*`) y cualquier clave de API de video/social.

### F4 — Botble es software comercial (criticidad: informativa)
Botble CMS se licencia vía CodeCanyon (~USD 29 por sitio; el core está en Packagist como `botble/cms`). Si el proyecto se pensaba para producción/comercial, hay que contemplar la licencia. Alternativas open-source equivalentes: Filament, Statamic (core), October CMS, o Laravel puro + un generador CRUD.

### F5 — Higiene del repo (criticidad: baja)
- Falta `.gitignore` (riesgo futuro de commitear `.env`, `vendor/`, `storage/`).
- Falta archivo `LICENSE` pese a declarar MIT en composer.json.
- README documenta `php artisan db:seed`, `storage:link`, etc. — comandos imposibles hoy porque no hay proyecto Laravel.
- Secciones de despliegue (Vercel/Railway/Heroku) sin infraestructura que las respalde.

### F6 — Sin trazas de actividad adicional (criticidad: informativa)
0 issues, 0 PRs, 0 forks, 0 releases, 0 paquetes, 0 Actions. El repo de GitHub tiene una sola rama (`main`) y ningún contenido oculto en refs.

---

## 6. Veredicto

| Pregunta | Respuesta |
|---|---|
| ¿Cómo estaba compuesto el proyecto? | Por 3 archivos de documentación/spec. Nunca hubo código en git. |
| ¿Se perdió código? | En este repo, no: nunca se subió. Si existió una v4.2.0 real, vive fuera de GitHub (PC local, otro hosting, u otro repo). |
| ¿Dónde está lo de "videos virales"? | No está en este repo. El candidato más probable entre tus repos es `nexus-shorts-ai`. |
| ¿Se puede instalar hoy? | No. El composer.json es inválido (Botble 10.x no existe) y no hay esqueleto Laravel. |

---

## 7. Opciones para seguir adelante

### Opción A — Reconstruir UltraNews desde cero (recomendada)
Usar el README existente como spec y crear el proyecto real, modernizado:
- Laravel 12 (PHP 8.3) en lugar de Laravel 10 EOL.
- Decidir CMS: Botble 7.6 real (con licencia) **o** Laravel + Filament (open source, admin panel potente, ideal para un CMS de noticias).
- Modelo de datos: `articles`, `categories`, `tags`, `users`, con API REST como documenta el README.
- **Módulo de videos virales** (lo que recordás): ingestión vía YouTube Data API / TikTok, embeds de video en noticias, ranking de "tendencias", campos `video_url`, `views`, `source_platform`.
- `.gitignore`, `LICENSE`, seeders, tests y CI básicos desde el día 1.

### Opción B — Corregir solo la especificación (rápida)
Arreglar `composer.json` a versiones reales (p. ej. `botble/cms: ^7.6` + `laravel/framework: ^13.0` + PHP `^8.3`), actualizar el README para que refleje la realidad (proyecto en etapa de planificación), agregar `.gitignore` y `LICENSE`. Queda un repo honesto y listo para cuando se implemente.

### Opción C — Buscar el código original en otro lado
Si creés que la v4.2.0 real existió: revisar tu PC local (buscar carpetas `ultranews`), hosting/FTP (cPanel, etc.), u otros repos (`nexus-shorts-ai`, `instadown`). Puedo auditar cualquiera de esos repos si me lo pedís.

---

*Informe generado automáticamente durante la auditoría del 2026-10-25 sobre la rama `arena/eef9643e-ultranews` (idéntica a `main` @ `670c383`).*
