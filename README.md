# LoanManager

A structured application for managing customer and loan data, emphasizing modular design, clear separation of concerns, and secure data handling, following clean architecture principles adaptable to both backend and frontend.

## Estructura del Proyecto

El proyecto está organizado en tres secciones principales:

### Backend

Ubicado en `/Backend`. Contains the server application built with Laravel.

#### Bounded Contexts

| Bounded Context | Descripción | Documentación |
|-----------------|-------------|-------------|
| SharedKernel | Core compartido con VOs, excepciones, puertos y servicios | [README](./Backend/app/SharedKernel/Documentation/README.md) |
| CustomerBC | Gestión de clientes | [README](./Backend/app/CustomerBC/Documentation/README.md) |
| LoanBC | Gestión de préstamos e intereses | [README](./Backend/app/LoanBC/Documentation/README.md) |
| PaymentBC | Procesamiento de pagos | [README](./Backend/app/PaymentBC/Documentation/README.md) |
| ReportBC | Reportes consolidados | [README](./Backend/app/ReportBC/Documentation/README.md) |
| UserBC | Autenticación de usuarios | [README](./Backend/app/UserBC/Documentation/README.md) |

#### Tecnologías Backend
- **Framework**: Laravel 11+
- **PHP**: 8.2+
- **ORM**: Eloquent
- **API**: RESTful

### Endpoints Overview

| Método | Ruta | Descripción |
|--------|-----|-------------|
| POST | /api/auth/login | Login de usuario |
| POST | /api/auth/logout | Logout (auth) |
| GET | /api/auth/me | Usuario actual (auth) |
| POST | /api/customers | Crear cliente |
| GET | /api/customers | Listar clientes |
| GET | /api/customers/{id} | Ver cliente |
| PUT | /api/customers/{id} | Actualizar cliente |
| GET | /api/customers/summary | Resumen clientes |
| POST | /api/loans | Crear préstamo |
| GET | /api/loans | Listar préstamos |
| GET | /api/loans/{id} | Ver préstamo |
| POST | /api/payments | Procesar pago |
| GET | /api/reports/projected-vs-actual | Reporte saldos proyectados vs reales |
| GET | /api/reports/collection-availability | Reporte disponibilidad de recaudo |
| GET | /api/reports/client-profitability | Reporte ROI por cliente |

---

### Frontend

Ubicado en `/Frontend`. Pendiente de implementación.

#### Tecnologías Frontend
- **Estado**: Pendiente de definición
- **Framework**: Por definir

---

### Base de Datos

Ubicado en `/Database`. Schemas y documentación de la base de datos.

#### Tecnologías DB
- **Motor**: MySQL / PostgreSQL
- **ORM**: Eloquent (Laravel)

---

## Getting Started

### Requisitos Previos

- PHP 8.2+
- Composer
- Laravel 11+
- Node.js (para frontend, cuando se defina)

### Instalación del Backend

```bash
cd Backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### Corriendo la aplicación

```bash
php artisan serve
# Acceder a http://localhost:8000

# Endpoints disponibles:
# - /api/customers/*
# - /api/loans/*
# - /api/payments
# - /api/reports/*
# - /api/auth/login
```

### Configuración de Excepciones

Las excepciones se manejan automáticamente mediante el middleware `HandleExceptions` registrado globalmente.

### Colecciones Postman

Cada BC contiene su propia colección Postman en su documentación para probar los endpoints.

---

## Arquitectura

El proyecto sigue **Domain Driven Design (DDD)** con clean architecture:

```
├── Domain/           → Entidades, Value Objects, Reglas de negocio
├── Application/     → Use Cases, Commands, DTOs
├── Infrastructure/   → Modelos Eloquent, Implementaciones de repositorios
└── Presentation/     → Controllers, Request Mappers, Middleware
```

### Patrones Implementados

- **Value Objects**: Inmutables con validación incorporada
- **Repositories**: Segregación de interfaces (ISP)
- **CQRS**: Commands para escrituras, Use Cases para lecturas
- **Exception Handling**: Jerarquía organizada por tipo

---

## Contributing

1. Crear feature branch desde `main`
2. Implementar siguiendo las guías de arquitectura
3. Agregar tests unitarios
4. Crear PR para revisión

## Licencia

MIT