# Quick Docker Start

## 1. Start Docker (if not running)
```bash
sudo systemctl start docker
```

## 2. Deploy
```bash
cd /home/jolman/Projects/LoanManager/loan_manager
./docker-deploy.sh
```

## 3. Access
- **Web**: http://localhost:8000
- **API**: http://localhost:8000/api

## 4. Manual Commands

### Start containers
```bash
docker compose up -d
```

### View logs
```bash
docker compose logs -f
```

### Run artisan commands
```bash
docker compose exec app php artisan [command]
```

### Stop
```bash
docker compose stop
```

---

## Files Created

| File | Purpose |
|------|---------|
| `Dockerfile` | PHP-FPM container with Redis extension |
| `docker-compose.yml` | Multi-container setup (app, nginx, mariadb, redis, queue, scheduler) |
| `docker/nginx/app.conf` | Nginx configuration |
| `.dockerignore` | Exclude files from build |
| `.env.example` | Example environment for Docker |
| `docker-deploy.sh` | Automated deployment script |
| `README-Docker.md` | Full deployment guide |
| `composer.json` | Added docker:* scripts |

---

## Services in Docker

```
loanmanager_nginx    (port 8000 → 80)
loanmanager_app      (PHP-FPM)
loanmanager_mariadb  (port 3306)
loanmanager_redis   (port 6379)
loanmanager_queue   (Queue worker)
loanmanager_scheduler (Cron jobs)
```

The `CheckOverdueLoans` job will run daily via scheduler!
