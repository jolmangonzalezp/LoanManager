# LoanManager Backend

API REST para gestión de préstamos con arquitectura DDD (Domain Driven Design).

## Stack

- Laravel 11+
- PHP 8.2+
- Eloquent ORM

## Bounded Contexts

| BC | Descripción | Documentación |
|----|-----------|--------------|
| SharedKernel | VOs, excepciones, puertos | [docs/SharedKernel/README.md](../docs/SharedKernel/README.md) |
| CustomerBC | Gestión de clientes | [docs/bc/CustomerBC/README.md](../docs/bc/CustomerBC/README.md) |
| LoanBC | Préstamos e intereses | [docs/bc/LoanBC/README.md](../docs/bc/LoanBC/README.md) |
| PaymentBC | Procesamiento de pagos | [docs/bc/PaymentBC/README.md](../docs/bc/PaymentBC/README.md) |
| ReportBC | Reportes | [docs/bc/ReportBC/README.md](../docs/bc/ReportBC/README.md) |
| UserBC | Autenticación | [docs/bc/UserBC/README.md](../docs/bc/UserBC/README.md) |

## Getting Started

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

## Endpoints

| Método | Ruta | Descripción |
|--------|-----|------------|
| POST | /api/auth/login | Login |
| GET | /api/customers | Clientes |
| GET | /api/loans | Préstamos |
| POST | /api/payments | Pago |
| GET | /api/reports/* | Reportes |

Ver [Documentación](../docs/bc/*BC/README.md) de cada BC para detalles.