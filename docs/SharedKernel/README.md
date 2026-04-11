# SharedKernel

## Descripción
El SharedKernel contiene componentes compartidos entre todos los Bounded Contexts (BCs). Proporciona la base sobre la cual se construyen los demás BCs.

## Estructura

```
SharedKernel/
├── Application/
│   └── Exceptions/       → Excepciones operativas
├── Domain/
│   ├── Exceptions/    → Excepciones de dominio
│   ├── Ports/         → Interfaces de servicios
│   └── ValueObjects/  → VOs reutilizables
├── Infrastructure/
│   ├── Exceptions/    → Excepciones de infraestructura
│   └── Services/      → Implementaciones de servicios
└── Presentation/
    ├── Exceptions/     → Mapper de excepciones
    └── Middleware/     → Middleware de manejo de errores
```

## Value Objects

### Direcciones

| VO | Descripción |
|----|-------------|
| `AddressVO` | Dirección postal |

### Fechas

| VO | Descripción |
|----|-------------|
| `DateVO` | Fecha ( DateTimeImmutable) |

### Identificadores

| VO | Descripción |
|----|-------------|
| `IdVO` | Interface para identificadores |
| `UuidVO` | UUID v7 |

### Monetarios

| VO | Descripción |
|----|-------------|
| `Currency` | Enum de monedas (COP, USD, EUR) |
| `MoneyVO` | Valor monetario con operaciones |

### Personas

| VO | Descripción |
|----|-------------|
| `PersonVO` | Datos completos de persona |
| `NameVO` | Nombre (firstName, lastName, secondLastName, middleName) |
| `DniType` | Enum de tipos de documento (CC, CE, NIT, PASSPORT) |
| `DniVO` | Documento de identidad |
| `PhoneVO` | Teléfono con código de país |
| `EmailVO` | Correo electrónico |

## Excepciones

### Jerarquía de Excepciones

```
RuntimeException
├── DomainException (abstract)
│   ├── InvalidDniException
│   ├── InvalidEmailException
│   ├── InvalidNameException
│   ├── InvalidAddressException
│   ├── InvalidPhoneException
│   ├── InvalidUuidException
│   ├── InvalidMoneyException
│   ├── AggregateNotFoundException
│   ├── EntityNotFoundException
│   └── BusinessRuleViolationException
│
├── ApplicationException (abstract)
│   ├── NotFoundException
│   ├── ValidationException
│   ├── UnauthorizedException
│   ├── ForbiddenException
│   ├── ConflictException
│   ├── ServiceUnavailableException
│   └── ConflictException
│
└── InfrastructureException (abstract)
    ├── CacheException
    ├── ExternalServiceException
    ├── RepositoryException
    └── DatabaseException
```

### Excepciones Específicas de BCs

- `CustomerNotFoundException` (CustomerBC)
- `CustomerAlreadyExistsException` (CustomerBC)

## Puertos (Interfaces)

### Servicios

| Puerto | Descripción |
|--------|-------------|
| `EncryptionService` | Encriptación y hashing |
| `MaskingService` | Enmascaramiento de datos sensibles |

## Servicios de Infraestructura

| Servicio | Descripción |
|----------|-------------|
| `LaravelEncryptionService` | Implementación con Crypt y Hash de Laravel |
| `LaravelMaskingService` | Implementación de enmascaramiento |

## Ejemplos de Uso

### Enmascaramiento

```php
$maskingService = app(MaskingService::class);

// Enmascarar email
$masked = $maskingService->maskEmail('juan@example.com'); // j***@example.com

// Enmascarar teléfono
$masked = $maskingService->maskPhone('3001234567'); // ******4567

// Enmascarar DNI
$masked = $maskingService->maskDni('12345678', 'CC'); // ****5678

// Enmascarar genérico
$masked = $maskingService->mask('texto123', 4); // ****to123
```

### Encriptación

```php
$encryptionService = app(EncryptionService::class);

// Encriptar
$encrypted = $encryptionService->encrypt('password'); // string encriptado

// Desencriptar
$decrypted = $encryptionService->decrypt($encrypted);

// Hash
$hash = $encryptionService->hash('password'); // bcrypt hash

// Verificar
$valid = $encryptionService->verify('password', $hash); // bool
```

## Presentation

### ExceptionMapper

El `ExceptionMapper` transforma excepciones en respuestas JSON estructuradas:

```json
{
    "error": {
        "type": "APPLICATION_ERROR",
        "code": "CUSTOMER_NOT_FOUND",
        "message": "Cliente no encontrado"
    },
    "trace_id": "uuid"
}
```

### HandleExceptions Middleware

Middleware global para manejo centralizado de excepciones:

```php
Route::middleware('handle.exceptions')->group(function () {
    // Routes
});
```

## Mapeo de HTTP Status

| Excepción | Status |
|-----------|--------|
| NotFoundException, EntityNotFoundException | 404 |
| ValidationException, DomainException | 422 |
| UnauthorizedException | 401 |
| ForbiddenException | 403 |
| ConflictException | 409 |
| ServiceUnavailableException, InfrastructureException | 503 |
| Default | 500 |