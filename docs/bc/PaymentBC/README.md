# Payment Bounded Context

## Descripción
BC responsable del procesamiento de pagos de préstamos.

## Entidades

### Payment
- `id`: UUID (PaymentIdVO)
- `loanId`: UUID (LoanIdVO) - Referencia al préstamo
- `amount`: MoneyVO - Monto del pago
- `paymentDate`: DateVO - Fecha del pago
- `createdAt`: DateVO - Fecha de creación
- `status`: PaymentStatus - Estado del pago
- `interestPaid`: MoneyVO - Interés pagado
- `capitalPaid`: MoneyVO - Capital pagado

## Value Objects

| VO | Descripción |
|----|-------------|
| `PaymentIdVO` | Identificador único UUID v7 |
| `PaymentStatus` | Estado: pending/applied/rejected |

## Reglas de Negocio

1. **Monto válido**: El monto debe ser mayor a 0.
2. **Aplicación automática**: El pago se aplica primero a intereses, luego a capital.
3. **Préstamo requerido**: El pago debe estar asociado a un préstamo existente.
4. **Actualización de deuda**: Al aplicar el pago, se actualiza la deuda del préstamo.

## API

### Endpoints

| Método | Ruta | Descripción |
|--------|-----|-------------|
| POST | `/api/payments` | Procesar pago |

### Request Examples

#### Procesar Pago
```json
POST /api/payments
{
    "loan_id": "019d7859-bdbc-719c-a00b-c07df5c88862",
    "amount": 50000,
    "payment_date": "2026-04-10"
}
```

#### Campos
- `loan_id` (requerido): UUID del préstamo
- `amount` (requerido): Monto del pago en pesos
- `payment_date` (opcional): Fecha del pago (default: hoy)
- `currency` (opcional): Moneda (default: COP)

#### Respuesta Exitosa
```json
{
    "id": "019d78a1-5c2d-71ae-b12f-d8e4f2a9c3b5",
    "loan_id": "019d7859-bdbc-719c-a00b-c07df5c88862",
    "amount": 50000,
    "payment_date": "2026-04-10",
    "status": "applied",
    "interest_paid": 20000,
    "capital_paid": 30000,
    "created_at": "2026-04-10T10:30:00Z"
}
```

### Excepciones

| Código | Descripción |
|--------|-------------|
| INVALID_AMOUNT | El monto debe ser mayor a 0 |
| LOAN_NOT_FOUND | Préstamo no encontrado |
| LOAN_INACTIVE | El préstamo no está activo |

## Estructura de Archivos

```
PaymentBC/
├── Domain/
│   ├── Entities/
│   │   └── Payment.php
│   ├── ValueObjects/
│   │   ├── PaymentIdVO.php
│   │   └── PaymentStatus.php
│   └── Repositories/
│       ├── PaymentCreator.php
│       ├── PaymentFinderById.php
│       └── PaymentFinderByLoanId.php
├── Application/
│   ├── Commands/
│   │   └── ProcessPaymentCommand.php
│   ├── DTOs/
│   │   └── PaymentResponse.php
│   └── UseCases/
│       └── ProcessPaymentUseCase.php
├── Infrastructure/
│   ├── Models/
│   │   └── PaymentModel.php
│   └── Repositories/
│       ├── EloquentPaymentCreator.php
│       ├── EloquentPaymentFinderByLoanId.php
│       └── PaymentMapper.php
├── Presentation/
│   └── Controllers/
│       └── PaymentController.php
└── Providers/
    └── PaymentServiceProvider.php
```

## Integración

Este BC depende de LoanBC para actualizar el estado del préstamo.

1. Injectar repositorios necesarios:
```php
use App\PaymentBC\Domain\Repositories\PaymentFinderByLoanId;
```

2. El BC automáticamente:
   - Busca el préstamo por ID
   - Calcula intereses pendientes
   - Aplica el pago (primero intereses, luego capital)
   - Actualiza el estado del préstamo