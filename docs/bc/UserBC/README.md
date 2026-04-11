# User Bounded Context

## Descripción
BC responsable de la autenticación y gestión de usuarios del sistema.

## Entidades

### User
- `id`: UUID (UserIdVO)
- `name`: string
- `email`: EmailVO
- `password`: string (hash)
- `enabled`: bool
- `createdAt`: DateVO

## Value Objects

| VO | Descripción |
|----|-------------|
| `UserIdVO` | Extiende de UuidVO |

## Reglas de Negocio

1. **Email único**: No puede haber dos usuarios con el mismo email.
2. **Usuario habilitado**: Solo usuarios con `enabled=true` pueden autenticarse.
3. **Password hash**: Las contraseñas se almacenan hasheadas con bcrypt.

## API

### Endpoints

| Método | Ruta | Descripción | Auth |
|--------|-----|-------------|------|
| POST | `/api/auth/login` | Iniciar sesión | No |
| POST | `/api/auth/logout` | Cerrar sesión | Sí |
| GET | `/api/auth/me` | Obtener usuario actual | Sí |

### Request Examples

#### Login
```json
POST /api/auth/login
{
    "email": "usuario@ejemplo.com",
    "password": "password123"
}
```

#### Response
```json
{
    "user": {
        "id": "uuid",
        "name": "Juan Pérez",
        "email": "juan@ejemplo.com",
        "enabled": true
    },
    "token": "eyJpdiI6Ij..."
}
```

### Excepciones

| Código | Descripción |
|--------|-------------|
| INVALID_CREDENTIALS | Email o password inválidos |
| USER_DISABLED | Usuario deshabilitado |
| USER_NOT_FOUND | Usuario no encontrado |

## Estructura de Archivos

```
UserBC/
├── Domain/
│   ├── Entities/
│   │   └── User.php
│   ├── ValueObjects/
│   │   └── UserIdVO.php
│   └── Repositories/
│       ├── UserCreator.php
│       ├── UserFinderByEmail.php
│       └── UserFinderById.php
├── Application/
│   ├── Commands/
│   │   ├── CreateUserCommand.php
│   │   └── LoginCommand.php
│   ├── DTOs/
│   │   └── UserResponse.php
│   └── UseCases/
│       ├── CreateUserUseCase.php
│       └── LoginUseCase.php
├── Infrastructure/
│   ├── Models/
│   │   └── UserModel.php
│   └── Repositories/
│       ├── EloquentUserCreator.php
│       ├── EloquentUserFinderByEmail.php
│       └── UserMapper.php
├── Presentation/
│   └── Controllers/
│       └── AuthController.php
└── Providers/
    └── UserServiceProvider.php
```

## Integración

Para usar este BC en otros contextos:

1. Injectar repositorios necesarios:
```php
use App\UserBC\Domain\Repositories\UserFinderByEmail;
```

2. Verificar autenticación con middleware `auth:sanctum`:
```php
Route::middleware('auth:sanctum')->group(function () {
    // rutas protegidas
});
```