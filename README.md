# UltraNews v4.2.0

CMS Laravel basado en Botble - Sistema completo de gestión de noticias.

## Características

- ✅ Gestión de artículos/noticias
- ✅ Categorías y etiquetas
- ✅ Usuarios y permisos
- ✅ SEO optimizado
- ✅ API REST
- ✅ Panel de administración
- ✅ Responsive design

## Requisitos

- PHP 8.1+
- Laravel 10+
- MySQL 8.0+
- Composer

## Instalación

### 1. Clonar Repositorio
```bash
git clone https://github.com/jacxas/ultranews.git
cd ultranews
```

### 2. Instalar Dependencias
```bash
composer install
```

### 3. Configurar Entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar Base de Datos
```bash
# Editar .env con tus credenciales de BD

# Ejecutar migraciones
php artisan migrate

# Seeders (opcional)
php artisan db:seed
```

### 5. Ejecutar en Desarrollo
```bash
php artisan serve
```

Accede a: http://localhost:8000

## Estructura del Proyecto

```
ultranews/
├── app/              # Lógica de aplicación
├── database/         # Migraciones y seeders
├── resources/        # Vistas y assets
├── routes/           # Rutas de la aplicación
├── storage/          # Archivos generados
├── tests/            # Tests
└── config/           # Configuración
```

## Comandos Disponibles

```bash
php artisan serve                  # Ejecutar servidor
php artisan migrate                # Ejecutar migraciones
php artisan db:seed                # Ejecutar seeders
php artisan cache:clear            # Limpiar caché
php artisan config:cache           # Cachear configuración
php artisan storage:link           # Crear link de storage
```

## Despliegue

### En Vercel (con PHP)
```bash
vercel deploy
```

### En Railway
```bash
railway up
```

### En Heroku
```bash
heroku create ultranews
git push heroku main
```

## Configuración

### .env Variables Importantes
```
APP_NAME=UltraNews
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ultranews
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=
MAIL_PASSWORD=
```

## API Endpoints

### Noticias
- `GET /api/articles` - Listar noticias
- `GET /api/articles/{id}` - Obtener noticia
- `POST /api/articles` - Crear noticia (admin)
- `PUT /api/articles/{id}` - Actualizar noticia (admin)
- `DELETE /api/articles/{id}` - Eliminar noticia (admin)

### Categorías
- `GET /api/categories` - Listar categorías
- `GET /api/categories/{id}` - Obtener categoría

## Troubleshooting

### Error: "SQLSTATE[HY000]"
- Verificar que MySQL está corriendo
- Verificar credenciales en `.env`

### Error: "No application encryption key"
- Ejecutar: `php artisan key:generate`

### Error: "Class not found"
- Ejecutar: `composer dump-autoload`

## Documentación

- [Laravel](https://laravel.com/docs)
- [Botble CMS](https://botble.com/docs)
- [MySQL](https://dev.mysql.com/doc/)

## Licencia

MIT

## Autor

jacxas

---

Última actualización: 2026-09-13
