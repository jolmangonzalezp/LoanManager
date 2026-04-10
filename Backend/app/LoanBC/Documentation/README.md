# Loan Bounded Context

## Descripción
BC responsable de la gestión de préstamos y crédito a clientes.

## Entidades

### Loan
- `id`: UUID (LoanIdVO)
- `customerId`: UUID (CustomerIdVO) - Referencia a cliente
- `originalCapital`: MoneyVO - Capital original del préstamo
- `capital`: MoneyVO - Capital remaining
- `remainingDebt`: MoneyVO - Deuda pending
- `interestRate`: InterestRateVO - Tasa de interés
- `startDate`: DateVO - Fecha de inicio
- `dueDate`: DateVO - Fecha límite de pago
- `nextPaymentDate`: DateVO - Próxima fecha de pago
- `status`: LoanStatus - Estado del préstamo
- `paidCapital`: MoneyVO - Capital pagado
- `paidInterest`: MoneyVO - Interés pagado
- `createdAt`: DateVO - Fecha de creación

## Value Objects

| VO | Descripción |
|----|-------------|
| `LoanIdVO` | Identificador único UUID v7 |
| `MoneyVO` | Valor monetario (SharedKernel) |
| `InterestRateVO` | Tasa de interés (anual/mensual) |
| `LoanStatus` | Estado: active/paid/defaulted/cancelled |

## Reglas de Negocio

1. **Asociación a Cliente**: Cada préstamo debe estar asociado a un cliente existente.
2. **Monto válido**: El capital debe ser mayor a 0.
3. **Interés válido**: La tasa de interés debe estar entre 0% y 100%.
4. **Fechas requeridas**: Requiere fecha de inicio y fecha límite de pago.
5. **Automatización**: El préstamo queda activo al momento de creación.
6. **Reducción de capital**: El capital solo se reduce si los intereses están cubiertos.
7. **Capitalización**: Si el crédito pasa su fecha límite, el interés se suma al capital (entra en mora).

## API

### Endpoints

| Método | Ruta | Descripción |
|--------|-----|-------------|
| POST | `/api/loans` | Crear préstamo |
| GET | `/api/loans` | Listar todos los préstamos |
| GET | `/api/loans/{id}` | Obtener préstamo por ID |
| GET | `/api/loans/report` | Reporte de préstamos (cartera) |
| POST | `/api/loans/{id}/payment` | Registrar pago |

### Request Examples

#### Crear Préstamo
```json
POST /api/loans
{
    "customer_id": "019d7817-d8c2-71de-be75-d6e93fe0b6e9",
    "capital": 1000000,
    "interest_rate": 24,
    "start_date": "2026-04-10",
    "due_date": "2027-04-10"
}
```

#### Campos
- `customer_id` (requerido): UUID del cliente
- `capital` (requerido): Monto en pesos (se convierte a centavos)
- `interest_rate` (requerido): Tasa de interés anual (%)
- `start_date` (opcional): Fecha de inicio (default: hoy)
- `due_date` (requerido): Fecha límite de pago
- `currency` (opcional): Moneda (default: COP)

#### Respuesta Exitosa
```json
{
    "id": "019d7859-bdbc-719c-a00b-c07df5c88862",
    "customer_id": "019d7817-d8c2-71de-be75-d6e93fe0b6e9",
    "capital": {"amount": 100000000, "currency": "COP"},
    "remaining_debt": {"amount": 100000000, "currency": "COP"},
    "interest_rate": {"annual": 24, "monthly": 2},
    "start_date": "2026-04-10",
    "due_date": "2027-04-10",
    "next_payment_date": "2026-05-10",
    "status": "active",
    "paid_capital": {"amount": 0, "currency": "COP"},
    "paid_interest": {"amount": 0, "currency": "COP"},
    "created_at": "2026-04-10 17:04:00"
}
```

#### Listar Préstamos
```json
GET /api/loans
```

#### Respuesta
```json
[
    {
        "id": "019d7859-bdbc-719c-a00b-c07df5c88862",
        "customer_id": "019d7817-d8c2-71de-be75-d6e93fe0b6e9",
        "capital": {"amount": 100000000, "currency": "COP"},
        "remaining_debt": {"amount": 100000000, "currency": "COP"},
        "interest_rate": {"annual": 24, "monthly": 2},
        "start_date": "2026-04-10",
        "due_date": "2027-04-10",
        "next_payment_date": "2026-05-10",
        "status": "active",
        "paid_capital": {"amount": 0, "currency": "COP"},
        "paid_interest": {"amount": 0, "currency": "COP"},
        "created_at": "2026-04-10 17:04:00"
    },
    ...
]
```

#### Reporte de Cartera
```json
GET /api/loans/report
```

#### Respuesta
```json
{
    "total_loans": 100,
    "active_loans": 85,
    "paid_loans": 10,
    "defaulted_loans": 5,
    "total_capital": 100000000,
    "total_remaining_debt": 85000000,
    "total_paid_capital": 15000000,
    "total_paid_interest": 25000000
}
```

#### Registrar Pago
```json
POST /api/loans/019d7859-bdbc-719c-a00b-c07df5c88862/payment
{
    "amount": 25000
}
```

#### Respuesta
```json
{
    "id": "019d7859-bdbc-719c-a00b-c07df5c88862",
    "capital": {"amount": 48500000, "currency": "COP"},
    "remaining_debt": {"amount": 48500000, "currency": "COP"},
    "interest_rate": {"annual": 24, "monthly": 2},
    "next_payment_date": "2026-06-10",
    "status": "active",
    "paid_capital": {"amount": 1500000, "currency": "COP"},
    "paid_interest": {"amount": 1000000, "currency": "COP"},
    ...
}
```

### Códigos de Error

| Código | Descripción | HTTP Status |
|--------|-------------|-------------|
| LOAN_NOT_FOUND | Préstamo no encontrado | 404 |
| CUSTOMER_NOT_FOUND | Cliente no encontrado | 404 |
| INVALID_CAPITAL | Capital inválido | 422 |
| INVALID_INTEREST_RATE | Tasa de interés inválida | 422 |
| LOAN_INACTIVE | El préstamo no está activo | 422 |

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
        "name": "Loan BC API",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    "item": [
        {
            "name": "Create Loan",
            "request": {
                "method": "POST",
                "url": "{{base_url}}/api/loans",
                "header": [
                    {"key": "Content-Type", "value": "application/json"}
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n    \"customer_id\": \"019d7817-d8c2-71de-be75-d6e93fe0b6e9\",\n    \"capital\": 1000000,\n    \"interest_rate\": 24,\n    \"start_date\": \"2026-04-10\",\n    \"due_date\": \"2027-04-10\"\n}"
                }
            }
        },
        {
            "name": "List Loans",
            "request": {
                "method": "GET",
                "url": "{{base_url}}/api/loans"
            }
        },
        {
            "name": "Get Loan Report",
            "request": {
                "method": "GET",
                "url": "{{base_url}}/api/loans/report"
            }
        },
        {
            "name": "Get Loan By ID",
            "request": {
                "method": "GET",
                "url": "{{base_url}}/api/loans/:id"
            }
        },
        {
            "name": "Make Payment",
            "request": {
                "method": "POST",
                "url": "{{base_url}}/api/loans/:id/payment",
                "header": [
                    {"key": "Content-Type", "value": "application/json"}
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n    \"amount\": 25000\n}"
                }
            }
        }
    ]
}
```

## Excepciones del BC

### Application Exceptions
- `LoanNotFoundException`: Préstamo no encontrado

### Domain Exceptions (Hereda de DomainException)
- `InvalidInterestException`: Interés inválido

## Servicios del BC

| Servicio | Descripción |
|----------|-------------|
| CreateLoanUseCase | Crear nuevo préstamo |
| GetLoanByIdUseCase | Obtener préstamo por ID |
| GetAllLoansUseCase | Listar todos los préstamos |
| GetLoanReportUseCase | Obtener reporte de cartera |
| MakePaymentUseCase | Registrar pago |