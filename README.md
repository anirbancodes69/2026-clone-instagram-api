# Insta API

A Laravel 12 backend API for an Instagram-like mobile application.

## Tech Stack

- **PHP 8.4** with PHP-FPM
- **Laravel 12** with Sanctum for API authentication
- **MySQL 8.0** database
- **Nginx** web server
- **Docker** for containerization

## Requirements

- Docker Desktop (with Docker Compose)
- macOS, Linux, or Windows with WSL2

## Quick Start

### 1. Clone and Setup

```bash
git clone <repository-url>
cd insta-api
```

### 2. Environment Configuration

Copy the example environment file and configure it:

```bash
cp .env.example .env
```

Update the `.env` file with Docker MySQL settings:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=insta_api
DB_USERNAME=insta_user
DB_PASSWORD=insta_pass
```

### 3. Start Docker Containers

```bash
docker compose up -d
```

This will start three services:
- **app** - PHP-FPM application container
- **web** - Nginx web server (exposed on port 8080)
- **db** - MySQL 8.0 database (exposed on port 3307)

### 4. Run Migrations

```bash
docker compose exec app php artisan migrate
```

### 5. Seed Database (Optional)

```bash
docker compose exec app php artisan db:seed
```

### 6. Access the Application

Open your browser and navigate to: **http://localhost:8080**

## Docker Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     Docker Network                       │
│                                                          │
│  ┌──────────┐      ┌──────────┐      ┌──────────┐       │
│  │   web    │      │   app    │      │    db    │       │
│  │  nginx   │─────▶│ php-fpm  │─────▶│  mysql   │       │
│  │  :80     │      │  :9000   │      │  :3306   │       │
│  └──────────┘      └──────────┘      └──────────┘       │
│       │                                    │             │
└───────┼────────────────────────────────────┼─────────────┘
        │                                    │
   localhost:8080                      localhost:3307
```

## Docker Services

| Service | Image | Port Mapping | Description |
|---------|-------|--------------|-------------|
| `app` | Custom (PHP 8.4-FPM) | 9000 (internal) | Laravel application |
| `web` | nginx:alpine | 8080:80 | Web server |
| `db` | mysql:8.0 | 3307:3306 | Database |

## Common Docker Commands

### Container Management

```bash
# Start all containers
docker compose up -d

# Stop all containers
docker compose down

# Restart all containers
docker compose restart

# View container logs
docker compose logs -f

# View specific service logs
docker compose logs -f app
```

### Artisan Commands

```bash
# Run migrations
docker compose exec app php artisan migrate

# Fresh migration with seeding
docker compose exec app php artisan migrate:fresh --seed

# Generate application key
docker compose exec app php artisan key:generate

# Clear all caches
docker compose exec app php artisan optimize:clear

# List routes
docker compose exec app php artisan route:list
```

### Composer Commands

```bash
# Install dependencies
docker compose exec app composer install

# Update dependencies
docker compose exec app composer update

# Dump autoload
docker compose exec app composer dump-autoload
```

### Database Access

```bash
# Access MySQL CLI
docker compose exec db mysql -u insta_user -p insta_api
# Password: insta_pass

# Or connect from host machine
mysql -h 127.0.0.1 -P 3307 -u insta_user -p insta_api
```

### Shell Access

```bash
# Access app container shell
docker compose exec app bash

# Access db container shell
docker compose exec db bash
```

## API Endpoints

### Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|:----:|-------------|
| POST | `/api/auth/register` | ❌ | Register new user |
| POST | `/api/auth/login` | ❌ | Login and get token |
| POST | `/api/auth/logout` | ✅ | Revoke current token |

### User

| Method | Endpoint | Auth | Description |
|--------|----------|:----:|-------------|
| GET | `/api/me` | ✅ | Get authenticated user |

### Posts

| Method | Endpoint | Auth | Description |
|--------|----------|:----:|-------------|
| GET | `/api/posts` | ✅ | List posts (paginated) |

## API Usage Examples

### Register a New User

```bash
curl -X POST http://localhost:8080/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "username": "johndoe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "name": "John Doe"
  }'
```

### Login

```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get Current User (Authenticated)

```bash
curl http://localhost:8080/api/me \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Get Posts (Authenticated)

```bash
curl http://localhost:8080/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

## API Response Format

All API responses follow a consistent structure:

```json
{
  "data": { ... },
  "message": "Success message",
  "errors": null
}
```

### Error Response Example

```json
{
  "data": null,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

## Test Credentials

After running seeders, you can use:

```
Email: test@example.com
Password: password
```

## Troubleshooting

### Container Won't Start

```bash
# Check container status
docker compose ps

# View logs for errors
docker compose logs app
```

### Database Connection Issues

1. Ensure the `db` container is healthy:
   ```bash
   docker compose ps
   ```

2. Wait for MySQL to initialize (first run may take 30-60 seconds)

3. Verify `.env` database settings match `docker-compose.yml`

### Permission Issues

```bash
# Fix storage permissions
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Rebuild Containers

```bash
# Rebuild without cache
docker compose build --no-cache

# Rebuild and restart
docker compose up -d --build
```

### Reset Everything

```bash
# Stop and remove containers, networks, and volumes
docker compose down -v

# Start fresh
docker compose up -d
docker compose exec app php artisan migrate:fresh --seed
```

## Project Structure

```
insta-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # API Controllers
│   │   └── Requests/Auth/      # Form Requests
│   ├── Models/                 # Eloquent Models
│   └── Traits/                 # Reusable Traits
├── database/
│   ├── factories/              # Model Factories
│   ├── migrations/             # Database Migrations
│   └── seeders/                # Database Seeders
├── docker/
│   ├── app/
│   │   ├── Dockerfile          # PHP-FPM image
│   │   └── entrypoint.sh       # Container startup script
│   └── nginx/
│       └── default.conf        # Nginx configuration
├── routes/
│   └── api.php                 # API Routes
├── docker-compose.yml          # Docker Compose config
└── .env                        # Environment variables
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
