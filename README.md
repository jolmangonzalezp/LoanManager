# LoanManager

Sistema de gestión de préstamos con arquitectura DDD.

## Estructura

- **Backend**: Laravel API → [Backend/README.md](./Backend/README.md)
- **Frontend**: Pendiente
- **Database**: Schemas → Pendiente

## Quick Start

```bash
cd Backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Ver [Backend](./Backend) para más detalles.