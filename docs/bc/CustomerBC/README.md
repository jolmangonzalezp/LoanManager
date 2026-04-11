# Customer Bounded Context

## Descripción
BC responsable de la gestión de clientes del sistema.

## Entidades

### Customer
- `id`: UUID (CustomerIdVO)
- `name`: NameVO - Nombre completo
- `dni`: DniVO - Documento de identidad
- `email`: EmailVO - Correo electrónico
- `phone`: PhoneVO - Teléfono
- `address`: AddressVO - Dirección
- `createdAt`: DateVO - Fecha de creación

## Value Objects

| VO | Descripción |
|----|-------------|
| `CustomerIdVO` | Identificador único UUID v7 |
| `NameVO` | Nombre (firstName, lastName, etc.) |
| `DniVO` | Documento de identidad |
| `EmailVO` | Correo electrónico |
| `PhoneVO` | Teléfono con código de país |
| `AddressVO` | Dirección postal |

## Reglas de Negocio

1. **DNI único**: No puede haber dos clientes con el mismo documento de identidad.
2. **Email único**: No puede haber dos clientes con el mismo correo electrónico.
3. **Teléfono único**: No puede haber dos clientes con el mismo teléfono.
4. **Datos requeridos**: Nombre, DNI y al menos un medio de contacto son obligatorios.

## API

### Endpoints

| Método | Ruta | Descripción |
|--------|-----|-------------|
| POST | `/api/customers` | Crear cliente |
| GET | `/api/customers` | Listar todos los clientes |
| GET | `/api/customers/{id}` | Obtener cliente por ID |
| PUT | `/api/customers/{id}` | Actualizar cliente |
| GET | `/api/customers/report` | Reporte de clientes |

### Request Examples

#### Crear Cliente
```json
POST /api/customers
{
    "first_name": "Juan",
    "last_name": "Pérez",
    "second_last_name": "García",
    "dni_type": "CC",
    "dni": "12345678",
    "email": "juan@example.com",
    "phone": "3001234567",
    "address": {
        "street": "Carrera 10 # 20-30",
        "city": "Bogotá",
        "department": "Cundinamarca",
        "country": "CO"
    }
}
```

#### Campos
- `first_name` (requerido): Primer nombre
- `last_name` (requerido): Apellido
- `second_last_name` (opcional): Segundo apellido
- `dni_type` (requerido): Tipo de documento (CC, CE, NIT, PASSPORT)
- `dni` (requerido): Número de documento
- `email` (opcional): Correo electrónico
- `phone` (opcional): Teléfono
- `address` (opcional): Dirección

#### Respuesta Exitosa
```json
{
    "id": "019d7817-d8c2-71de-be75-d6e93fe0b6e9",
    "name": {
        "first_name": "Juan",
        "last_name": "Pérez",
        "second_last_name": "García"
    },
    "dni": {
        "type": "CC",
        "number": "12345678"
    },
    "email": "juan@example.com",
    "phone": "573001234567",
    "address": {
        "street": "Carrera 10 # 20-30",
        "city": "Bogotá",
        "department": "Cundinamarca",
        "country": "CO"
    },
    "created_at": "2026-04-10T10:30:00Z"
}
```

#### Listar Clientes
```json
GET /api/customers
```

#### Respuesta
```json
[
    {
        "id": "019d7817-d8c2-71de-be75-d6e93fe0b6e9",
        "name": {"first_name": "Juan", "last_name": "Pérez"},
        "dni": {"type": "CC", "number": "12345678"},
        "email": "juan@example.com",
        "created_at": "2026-04-10T10:30:00Z"
    },
    ...
]
```

#### Reporte de Clientes
```json
GET /api/customers/report
```

#### Respuesta
```json
{
    "total_customers": 100,
    "customers_with_loans": 60,
    "customers_without_loans": 40
}
```

### Códigos de Error

| Código | Descripción | HTTP Status |
|--------|-------------|-------------|
| CUSTOMER_NOT_FOUND | Cliente no encontrado | 404 |
| CUSTOMER_ALREADY_EXISTS | Cliente ya existe | 409 |
| INVALID_DNI | Documento inválido | 422 |
| INVALID_EMAIL | Correo inválido | 422 |

### Respuesta de Error
```json
{
    "error": {
        "type": "APPLICATION_ERROR",
        "code": "CUSTOMER_ALREADY_EXISTS",
        "message": "Ya existe un cliente con este documento"
    },
    "trace_id": "uuid-v7"
}
```

## Excepciones del BC

### Application Exceptions
- `CustomerNotFoundException`: Cliente no encontrado
- `CustomerAlreadyExistsException`: Cliente ya existe

### Domain Exceptions
- `InvalidDniException`: Documento inválido
- `InvalidEmailException`: Correo inválido

## Servicios del BC

| Servicio | Descripción |
|----------|-------------|
| CreateCustomerUseCase | Crear nuevo cliente |
| GetCustomerByIdUseCase | Obtener cliente por ID |
| GetAllCustomersUseCase | Listar todos los clientes |
| UpdateCustomerUseCase | Actualizar cliente |
| GetCustomerReportUseCase | Obtener reporte de clientes |
| GetAllCustomersSummaryUseCase | Obtener resumen de clientes |

## Estructura de Archivos

```
CustomerBC/
├── Application/
│   ├── Commands/
│   │   ├── CreateCustomerCommand.php
│   │   └── UpdateCustomerCommand.php
│   ├── DTOs/
│   │   ├── CustomerResponse.php
│   │   ├── CustomerReportResponse.php
│   │   └── CustomerSummaryResponse.php
│   ├── UseCases/
│   │   ├── CreateCustomerUseCase.php
│   │   ├── GetCustomerByIdUseCase.php
│   │   ├── GetAllCustomersUseCase.php
│   │   ├── UpdateCustomerUseCase.php
│   │   ├── GetCustomerReportUseCase.php
│   │   └── GetAllCustomersSummaryUseCase.php
│   └── Exceptions/
│       ├── CustomerNotFoundException.php
│       └── CustomerAlreadyExistsException.php
├── Domain/
│   ├── Entities/
│   │   └── Customer.php
│   ├── ValueObjects/
│   │   └── CustomerIdVO.php
│   └── Repositories/
│       ├── CustomerCreator.php
│       ├── CustomerFinderById.php
│       ├── CustomerFinderAll.php
│       ├── CustomerUpdater.php
│       └── CustomerDniFinder.php
├── Infrastructure/
│   ├── Models/
│   │   └── CustomerModel.php
│   ├── Repositories/
│   │   ├── EloquentCustomerCreator.php
│   │   ├── EloquentCustomerFinderById.php
│   │   ├── EloquentCustomerFinderAll.php
│   │   ├── EloquentCustomerUpdater.php
│   │   └── EloquentCustomerDniFinder.php
│   ├── Persistence/
│   │   └── CustomerMapper.php
│   └── Migrations/
│       └── 2024_01_01_000001_create_customers_table.php
├── Presentation/
│   ├── Controllers/
│   │   └── CustomerController.php
│   └── Mappers/
│       ├── CreateCustomerRequestMapper.php
│       └── UpdateCustomerRequestMapper.php
└── Providers/
    └── CustomerServiceProvider.php
```