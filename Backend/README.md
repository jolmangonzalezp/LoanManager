# LoanManager Backend

API REST para gestión de préstamos con arquitectura DDD (Domain Driven Design).

## Stack

- Laravel 11+
- PHP 8.2+
- Eloquent ORM

## Bounded Contexts

| BC | Descripción | Documentación |
|----|-----------|--------------|
| SharedKernel | VOs, excepciones, puertos | [app/SharedKernel/Documentation](./app/SharedKernel/Documentation/README.md) |
| CustomerBC | Gestión de clientes | [app/CustomerBC/Documentation](./app/CustomerBC/Documentation/README.md) |
| LoanBC | Préstamos e intereses | [app/LoanBC/Documentation](./app/LoanBC/Documentation/README.md) |
| PaymentBC | Procesamiento de pagos | [app/PaymentBC/Documentation](./app/PaymentBC/Documentation/README.md) |
| ReportBC | Reportes | [app/ReportBC/Documentation](./app/ReportBC/Documentation/README.md) |
| UserBC | Autenticación | [app/UserBC/Documentation](./app/UserBC/Documentation/README.md) |

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

Ver [Documentación](./app/*BC/Documentation/README.md) de cada BC para detalles.