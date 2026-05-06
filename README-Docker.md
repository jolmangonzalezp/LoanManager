# LoanManager - Docker Deployment Guide

## Prerequisites

- Docker installed
- Docker Compose installed

## Quick Start

### 1. Clone/Navigate to Project
```bash
cd /home/jolman/Projects/LoanManager/loan_manager
```

### 2. Set Up Environment
```bash
cp .env.example .env
# Edit .env with your production values
```

### 3. Build & Start Containers
```bash
docker compose up -d --build
```

### 4. Generate App Key & Run Migrations
```bash
# Generate APP_KEY
docker compose exec app php artisan key:generate

# Run migrations
docker compose exec app php artisan migrate --force

# Seed database (optional)
docker compose exec app php artisan db:seed --force
```

### 5. Access the Application
- **Web**: http://localhost:8000
- **API**: http://localhost:8000/api

---

## Services

| Service | Description | Port |
|---------|-------------|------|
| **nginx** | Web server | 8000 → 80 |
| **app** | Laravel PHP-FPM | 9000 (internal) |
| **mariadb** | Database | 3306 |
| **redis** | Cache/Queue/Sessions | 6379 |
| **queue** | Queue worker | - |
| **scheduler** | Cron jobs (overdue check) | - |

---

## Common Commands

### View Logs
```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app
docker compose logs -f queue
docker compose logs -f nginx
```

### Restart Services
```bash
docker compose restart queue
docker compose restart scheduler
```

### Run Artisan Commands
```bash
docker compose exec app php artisan [command]
```

### Queue Management
```bash
# Restart queue worker
docker compose restart queue

# Check queue status
docker compose exec redis redis-cli
> LLEN laravel_database_queues:default
```

---

## Production Considerations

### 1. Security
- Set `APP_DEBUG=false`
- Use strong passwords for DB and Redis
- Generate a strong `APP_KEY`
- Use HTTPS (configure SSL in nginx)

### 2. Environment Variables
Update `.env` with production values:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_PASSWORD=strong_password
REDIS_PASSWORD=strong_password

QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
```

### 3. SSL/HTTPS
Update `docker/nginx/app.conf` to include SSL certificates.

### 4. Persistent Data
- MariaDB: `mariadb_data` volume
- Redis: `redis_data` volume

---

## Stopping & Cleanup

### Stop Containers
```bash
docker compose stop
```

### Stop & Remove Containers
```bash
docker compose down
```

### Stop & Remove + Delete Volumes (CAUTION: Deletes Data)
```bash
docker compose down -v
```

---

## Troubleshooting

### Permission Issues
```bash
docker compose exec app chown -R www-data:www-data /var/www/html/storage
docker compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
```

### Clear Cache
```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
```

### Rebuild Containers
```bash
docker compose down
docker compose up -d --build
```
