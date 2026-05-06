#!/bin/bash

# LoanManager Docker Deployment Script

set -e

echo "==> LoanManager Docker Deployment"
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "ERROR: Docker is not running. Please start Docker first:"
    echo "  sudo systemctl start docker"
    exit 1
fi

# Check if docker-compose exists
if ! command -v docker compose &> /dev/null; then
    echo "ERROR: docker compose not found"
    exit 1
fi

echo "==> Step 1: Setting up environment"
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
    echo "WARNING: Please edit .env with your production values!"
else
    echo ".env already exists"
fi

echo ""
echo "==> Step 2: Building Docker images"
docker compose build

echo ""
echo "==> Step 3: Starting containers"
docker compose up -d

echo ""
echo "==> Step 4: Waiting for services to start..."
sleep 10

echo ""
echo "==> Step 5: Generating APP_KEY"
docker compose exec -T app php artisan key:generate --no-interaction

echo ""
echo "==> Step 6: Running migrations"
docker compose exec -T app php artisan migrate --force

echo ""
echo "==> Step 7: Seeding database (optional)"
read -p "Do you want to seed the database? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    docker compose exec -T app php artisan db:seed --force
fi

echo ""
echo "==> Deployment complete!"
echo ""
echo "Services:"
echo "  - Web: http://localhost:8000"
echo "  - API: http://localhost:8000/api"
echo "  - MariaDB: localhost:3306"
echo "  - Redis: localhost:6379"
echo ""
echo "Useful commands:"
echo "  - View logs: docker compose logs -f"
echo "  - Stop: docker compose stop"
echo "  - Restart queue: docker compose restart queue"
echo "  - Artisan: docker compose exec app php artisan [command]"
echo ""
