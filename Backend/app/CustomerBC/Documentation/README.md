# Customer Bounded Context

## Descripción
BC responsable de la gestión de clientes del sistema de préstamos.

## Entidades

### Customer
- `id`: UUID (CustomerIdVO)
- `personalData`: PersonVO (nombre, DNI, teléfono, dirección, email)
- `createdAt`: DateVO
- `enabled`: bool

## Value Objects

| VO | Descripción |
|----|-------------|
| `CustomerIdVO` | Extiende de UuidVO |
| `NameVO` | Nombre completo (firstName, lastName, secondLastName, middleName opcional) |
| `DniVO` | Documento de identidad (número, tipo: CC/CE/NIT/PASSPORT) |
| `PhoneVO` | Teléfono (número, código país) |
| `AddressVO` | Dirección |
| `EmailVO` | Email (opcional) |

## Reglas de Negocio

1. **DNI único**: No puede haber dos clientes con el mismo tipo y número de documento.
2. **Segundo apellido obligatorio**: En Colombia el segundo apellido es requerido.
3. **Email opcional**: El email del cliente es opcional.

## API

### Endpoints

| Método | Ruta | Descripción |
|--------|-----|-------------|
| POST | `/api/customers` | Crear cliente |
| GET | `/api/customers` | Listar todos los clientes (completo) |
| GET | `/api/customers/{id}` | Obtener cliente por ID |
| PUT | `/api/customers/{id}` | Actualizar cliente |
| GET | `/api/customers/summary` | Lista clientes (id + nombre) |
| GET | `/api/customers/report` | Reporte de clientes |

### Request Examples

#### Crear Cliente
```json
POST /api/customers
{
    "first_name": "Juan",
    "last_name": "García",
    "second_last_name": "López",
    "middle_name": "Pablo",
    "dni_number": "12345678",
    "dni_type": "CC",
    "phone_number": "3001234567",
    "phone_country_code": "57",
    "address": "Calle 123 # 45-67, Bogotá",
    "email": "juan@example.com"
}
```

#### Respuesta Exitosa
```json
{
    "id": "uuid-v7",
    "name": "Juan Pablo García López",
    "dni": "123.456.783",
    "phone": "+573001234567",
    "address": "Calle 123 # 45-67, Bogotá",
    "email": "juan@example.com",
    "created_at": "2024-01-15 10:30:00",
    "enabled": true
}
```

#### listar Clientes
```json
GET /api/customers
```

#### Respuesta
```json
[
    {
        "id": "uuid-v7",
        "name": "Juan Pablo García López",
        "dni": "123.456.783",
        "phone": "+573001234567",
        "address": "Calle 123 # 45-67, Bogotá",
        "email": "juan@example.com",
        "created_at": "2024-01-15 10:30:00",
        "enabled": true
    },
    ...
]
```

#### Obtener Summary
```json
GET /api/customers/summary
```

#### Respuesta
```json
[
    {
        "id": "uuid-v7",
        "name": "Juan Pablo García López"
    },
    ...
]
```

#### Obtener Reporte
```json
GET /api/customers/report
```

#### Respuesta
```json
{
    "total_customers": 100,
    "total_customers_with_loan": 45,
    "total_customers_without_loan": 55
}
```

### Códigos de Error

| Código | Descripción | HTTP Status |
|--------|-------------|-------------|
| CUSTOMER_NOT_FOUND | Cliente no encontrado | 404 |
| CUSTOMER_ALREADY_EXISTS | Ya existe un cliente con ese DNI | 409 |
| INVALID_NAME | Nombre inválido | 422 |
| INVALID_DNI | DNI inválido | 422 |
| INVALID_EMAIL | Email inválido | 422 |
| INVALID_PHONE | Teléfono inválido | 422 |
| INVALID_ADDRESS | Dirección inválida | 422 |

### Respuesta de Error
```json
{
    "error": {
        "type": "APPLICATION_ERROR",
        "code": "CUSTOMER_NOT_FOUND",
        "message": "Cliente no encontrado"
    },
    "trace_id": "uuid-v7"
}
```

## Colección Postman

```json
{
    "info": {
        "name": "Customer BC API",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    "item": [
        {
            "name": "Create Customer",
            "request": {
                "method": "POST",
                "url": "{{base_url}}/api/customers",
                "header": [
                    {"key": "Content-Type", "value": "application/json"}
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n    \"first_name\": \"Juan\",\n    \"last_name\": \"García\",\n    \"second_last_name\": \"López\",\n    \"dni_number\": \"12345678\",\n    \"dni_type\": \"CC\",\n    \"phone_number\": \"3001234567\",\n    \"phone_country_code\": \"57\",\n    \"address\": \"Calle 123 # 45-67, Bogotá\",\n    \"email\": \"juan@example.com\"\n}"
                }
            }
        },
        {
            "name": "List Customers",
            "request": {
                "method": "GET",
                "url": "{{base_url}}/api/customers"
            }
        },
        {
            "name": "Get Customer Summary",
            "request": {
                "method": "GET",
                "url": "{{base_url}}/api/customers/summary"
            }
        },
        {
            "name": "Get Customer Report",
            "request": {
                "method": "GET",
                "url": "{{base_url}}/api/customers/report"
            }
        },
        {
            "name": "Get Customer By ID",
            "request": {
                "method": "GET",
                "url": "{{base_url}}/api/customers/:id"
            }
        },
        {
            "name": "Update Customer",
            "request": {
                "method": "PUT",
                "url": "{{base_url}}/api/customers/:id",
                "header": [
                    {"key": "Content-Type", "value": "application/json"}
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n    \"first_name\": \"Juan Updated\",\n    \"last_name\": \"García\",\n    \"second_last_name\": \"López\",\n    \"dni_number\": \"87654321\",\n    \"dni_type\": \"CC\",\n    \"phone_number\": \"3009876543\",\n    \"phone_country_code\": \"57\",\n    \"address\": \"Calle 456 # 78-90, Bogotá\",\n    \"email\": \"juanupdated@example.com\"\n}"
                }
            }
        }
    ]
}
```

## Excepciones del BC

### Application Exceptions
- `CustomerNotFoundException`: Cliente no encontrado
- `CustomerAlreadyExistsException`: Cliente ya existe con ese DNI

### Domain Exceptions (Hereda de DomainException)
- InvalidDniException, InvalidNameException, InvalidPhoneException, InvalidAddressException, InvalidEmailException

## Servicios del BC

| Servicio | Descripción |
|----------|-------------|
| CreateCustomerUseCase | Crear nuevo cliente |
| GetCustomerByIdUseCase | Obtener cliente por ID |
| GetAllCustomersUseCase | Listar todos los clientes |
| GetAllCustomersSummaryUseCase | Obtener lista resumida |
| GetCustomerReportUseCase | Obtener reporte |
| UpdateCustomerUseCase | Actualizar cliente |