# LoanManager

## Architecture

- **DDD with Bounded Contexts**: `CustomerBC`, `LoanBC`, `PaymentBC`, `ReportBC`
- **CQRS pattern**: Commands (writes) separated from Queries (reads)
- **Backend**: Laravel PHP (`Backend/`)
- **Frontend**: Vue.js 3 (`Frontend/`)

## Running the App

```bash
# Backend (Laravel)
cd Backend && php artisan serve --host=0.0.0.0 --port=8000

# Frontend (Vite)
cd Frontend && npm run dev
```

## Critical Discoveries (would likely be missed)

1. **MoneyVO**: valores en pesos COP, NO centavos (no multiply by 100)
2. **Tasa de interés**: es mensual, NO anual
3. **Customer names**: encriptados en DB → usar `CustomerNameProvider` para desencriptar
4. **DTOs**: cada clase en archivo separado (PSR-4 autoloader)
5. **loan_number**: formato `L-YYYY-####`

## Backend Patterns

### Request → Command Flow

```
Frontend payload → CreateLoanRequest::fromArray() → CreateLoanCommand → CreateLoanUseCase → Response
```

**Mapeo de campos requerido**:
- `customer_id` → `CustomerIdVO::fromString()`
- `capital` → `MoneyVO::create(int)`
- `interest_rate` → `InterestRateVO::createMonthly(int)`
- `start_date` → `DateVO::fromString()`
- `term` (meses) → `dueDate = startDate.addMonths(term)`

### LoanResponse Properties

```php
$response->setLoanNumber(string)     // setter
$response->getLoanNumber()         // getter
$response->setCustomerName(string) // pasa string, NO array
$response->toArray(customerId)     // requiere customerId como arg
```

### Routes

```
POST   /api/loans        → store()
GET    /api/loans        → index()
GET    /api/loans/{id}   → show()
PUT    /api/loans/{id}   → update()
POST   /api/loans/{id}/payments → makePayment()
GET    /api/reports/*     → ReportController
```

## Frontend Notes

- `useApi` composable para HTTP
- `formatCurrency` para display COP
- PaymentForm: monto en enteros (no /100)

## Recent Fixes

- `CreateLoanRequest`: corrige keys (`customer_id`, `interest_rate`)
- `UpdateLoanUseCase`: usa `setLoanNumber()`, `setCustomerName()`
- `LoanController`: pasa `getCustomerId()` a `toArray()`