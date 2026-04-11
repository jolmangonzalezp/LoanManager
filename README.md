# LoanManager

Sistema de gestión de préstamos con arquitectura DDD.

## Índice

- [Backend](./docs/Backend/README.md) - API REST Laravel
- [Bounded Contexts](./docs/bc)
  - [CustomerBC](./docs/bc/CustomerBC/README.md) - Gestión de clientes
  - [LoanBC](./docs/bc/LoanBC/README.md) - Préstamos e intereses
  - [PaymentBC](./docs/bc/PaymentBC/README.md) - Procesamiento de pagos
  - [ReportBC](./docs/bc/ReportBC/README.md) - Reportes
  - [UserBC](./docs/bc/UserBC/README.md) - Autenticación
- [SharedKernel](./docs/SharedKernel/README.md) - Componentes compartidos

## Quick Start

```bash
cd Backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```