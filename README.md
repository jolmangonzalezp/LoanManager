# LoanManager

A structured application for managing customer and loan data, emphasizing modular design, clear separation of concerns, and secure data handling, following clean architecture principles adaptable to both backend and frontend.

## Estructura del Proyecto

El proyecto está organizado en tres secciones principales:

### 📁 Backend

ubicado en `/Backend`. Contains the server application built with Laravel.

#### Bounded Contexts

| Bounded Context | Descripción |
|-----------------|-------------|
| [SharedKernel](./app/SharedKernel/README.md) | Core compartido con VOs, excepciones, puertos y servicios |
| [CustomerBC](./app/CustomerBC/Documentation/README.md) | Gestión de clientes |

#### TecnologíasBackend
- **Framework**: Laravel 11+
- **PHP**: 8.2+
- **ORM**: Eloquent
- **API**: RESTful

---

### 📁 Frontend

Ubicado en `/Frontend`. Pendiente de implementación.

#### TecnologíasFrontend
- **Estado**: Pendiente de definición
- **Framework**: Por definir

---

### 📁 Base de Datos

Ubicado en `/Database`. Schemas y documentación de la base de datos.

#### Tecnologías DB
- **Motor**: MySQL / PostgreSQL
- **ORM**: Eloquent (Laravel)

---

## Getting Started

### RequisitosPrevios

- PHP 8.2+
- Composer
- Laravel 11+
- Node.js (par frontend, cuando se defina)

### Instalación del Backend

```bash
cd Backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### Configuración de Excepciones

Las excepciones se manejan automáticamente mediante el middleware `HandleExceptions` registrado globalmente.

### ColeccionesPostman

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

---

## Licencia

Pendiente de definición.