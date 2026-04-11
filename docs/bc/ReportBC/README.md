# Report Bounded Context

## Descripción
BC responsable de generar reportes consolidados a partir de datos de múltiples BCs (CustomerBC, LoanBC, PaymentBC).

## Reportes Disponibles

### 1. Saldos Proyectados vs Reales
Compara la deuda esperada con la deuda real basada en pagos realizados.

**Endpoint**: `GET /api/reports/projected-vs-actual`

**Response**:
```json
[
    {
        "loan_id": "019d7859-bdbc-719c-a00b-c07df5c88862",
        "customer_id": "019d7817-d8c2-71de-be75-d6e93fe0b6e9",
        "customer_name": "Juan Pérez",
        "projected_debt": 1200000,
        "actual_debt": 1100000,
        "difference": 100000,
        "as_of_date": "2026-04-10"
    }
]
```

### 2. Disponibilidad de Recaudo
Muestra el total de capital disponible para recaudo (capital pendiente de todos los préstamos).

**Endpoint**: `GET /api/reports/collection-availability`

**Response**:
```json
[
    {
        "total_capital_available": 50000000,
        "currency": "COP",
        "as_of_date": "2026-04-10",
        "active_loans": 50,
        "total_loans": 60
    }
]
```

### 3. ROI por Cliente
Calcula la rentabilidad (interés pagado) por cliente.

**Endpoint**: `GET /api/reports/client-profitability`

**Response**:
```json
[
    {
        "customer_id": "019d7817-d8c2-71de-be75-d6e93fe0b6e9",
        "customer_name": "Juan Pérez",
        "total_paid": 240000,
        "interest_paid": 200000,
        "capital_paid": 800000,
        "loan_count": 2,
        "as_of_date": "2026-04-10"
    }
]
```

## Reglas de Negocio

1. **Consumo de datos**: Este BC consume repositorios de otros BCs pero no modifica datos.
2. **Fecha de corte**: Los reportes usan la fecha actual como fecha de corte.
3. **Solo préstamos activos**: Solo considera préstamos con estado `active`.

## Estructura de Archivos

```
ReportBC/
├── Application/
│   ├── UseCases/
│   │   ├── GetProjectedVsActualUseCase.php
│   │   ├── GetCollectionAvailabilityUseCase.php
│   │   └── GetClientProfitabilityUseCase.php
│   └── DTOs/
│       ├── ProjectedVsActualResponse.php
│       ├── CollectionAvailabilityResponse.php
│       └── ClientProfitabilityResponse.php
├── Presentation/
│   └── Controllers/
│       └── ReportController.php
└── Providers/
    └── ReportServiceProvider.php
```

## Dependencias

Este BC depende de:
- **CustomerBC**: Para información de clientes
- **LoanBC**: Para datos de préstamos
- **PaymentBC**: Para histórico de pagos

## Integración

Los reportes están expuestos en `/api/reports/*` (definido en `routes/reports.php`).

```php
// Usar en otro BC
use App\ReportBC\Application\UseCases\GetProjectedVsActualUseCase;

$useCase = app(GetProjectedVsActualUseCase::class);
$reports = $useCase->execute();
```