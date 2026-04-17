# LoanManager Development Context

## Goal

Continuar desarrollando y mejorando la aplicación Laravel LoanManager con arquitectura DDD. El trabajo actual se centra en el módulo de Reportes y asegurar que todos los endpoints funcionen correctamente.

## Instructions

- Seguir el patrón DDD con Bounded Contexts separados
- Mantener segregación de interfaces (ISP) en repositorios
- Usar CQRS pattern para escrituras
- Trabajar tanto en backend (PHP/Laravel) como frontend (Vue.js)

## Discoveries

1. **MoneyVO maneja valores en pesos COP** - No en centavos (se removió multiplicación x100)
2. **El campo `loan_number`** - Identificador alfanumérico único (formato L-YYYY-####) para buscar/préstamos
3. **La tasa de interés es mensual**, no anual
4. **Customer names están encriptados en DB** - Usar CustomerNameProvider para desencriptar
5. **Los DTOs requieren archivos separados** - PHP autoloader PSR-4 no soporta múltiples clases por archivo
6. **Los DTOs de Carbon/DateTime deben convertirse a string** - Para serialización JSON correcta

## Accomplished

**Backend - Reports Module:**
- ✅ ReportQueryService usa CustomerNameProvider para nombres desencriptados
- ✅ Creados archivos separados para DTOs (LoanProfitabilityDTO, LoanSummaryDTO, LoanDelinquencyDTO, PaymentDetailDTO, AuditEntryDTO)
- ✅ PaymentDetailDTO::fechaPago convertida a string (Carbon → format)
- ✅ Todos los endpoints de reportes funcionando: summary, portfolio, profitability, delinquency, active-loans, payment-history, kpis, monthly-collection, cash-flow

**Backend:**
- ✅ Corregido DniVO::create() - parámetros invertidos en CreateCustomerRequest y UpdateCustomerRequest
- ✅ Corregido DateVO::create() → DateVO::fromString() en LoanMapper
- ✅ Corregido InterestRateVO - removido uso de getCurrency()
- ✅ Corregido InterestCalculatorService - métodos faltantes (diffInDays, getYear, getMonth)
- ✅ Corregido LoanResponse::moneyToArray() - retorno correcto
- ✅ Corregido EloquentCustomerRepository - is_active → enabled
- ✅ Agregado campo loan_number con migración
- ✅ Creado LoanNumberGenerator - genera formato L-YYYY-####
- ✅ Migración para cambiar interest_rate de decimal(5,4) a decimal(6,4)
- ✅ Removido plazo (term) de la respuesta API
- ✅ Fixed PaymentServiceProvider namespace (Domain\Repositories → Domain\Repository)
- ✅ Fixed ProcessPaymentUseCase namespace y DateVO::create() → DateVO::fromString()
- ✅ Fixed PaymentController - removido *100 para conversiones de montos
- ✅ Added loan() relationship a PaymentModel
- ✅ Added customer() relationship a LoanModel
- ✅ Fixed PaymentResponse::moneyToArray() - removido getCurrency()
- ✅ Fixed EloquentPaymentCreator - inject PaymentMapper via constructor

**Frontend:**
- ✅ Customer detail muestra datos sin enmascarar (llama GetById separately)
- ✅ Customer summary KPIs se calculan en frontend
- ✅ Loan detail modal simplificado - encabezado solo con nombre + badge
- ✅ Grid 2x2: Monto | Saldo | Tasa | Progreso
- ✅ Removido campo plazo del formulario
- ✅ Campo monto solo acepta enteros
- ✅ Modal de edición cierra detalle antes de abrir formulario
- ✅ Fixed PaymentFormModal - removido /100 y referencias a term
- ✅ Fixed PaymentFormModal - customer name access (c.first_name en vez de c.name?.first_name)
- ✅ ReportsView con KPIs de summary, profitability y delinquency

## Relevant files / directories

```
Backend/
├── app/
│   ├── ReportBC/
│   │   ├── Application/DTO/
│   │   │   ├── PortfolioReportDTO.php
│   │   │   ├── ProfitabilityReportDTO.php
│   │   │   ├── LoanProfitabilityDTO.php
│   │   │   ├── DelinquencyReportDTO.php
│   │   │   ├── LoanDelinquencyDTO.php
│   │   │   ├── ActiveLoansReportDTO.php
│   │   │   ├── LoanSummaryDTO.php
│   │   │   ├── PaymentHistoryReportDTO.php
│   │   │   ├── PaymentDetailDTO.php
│   │   │   ├── KpiReportDTO.php
│   │   │   ├── MonthlyCollectionReportDTO.php
│   │   │   ├── CashFlowReportDTO.php
│   │   │   ├── AuditReportDTO.php
│   │   │   └── AuditEntryDTO.php
│   │   ├── Domain/Service/
│   │   │   └── ReportQueryService.php
│   │   └── Presenter/Controllers/
│   │       └── ReportController.php
│   └── LoanBC/
│       └── Domain/Repository/
│           └── CustomerNameProvider.php
├── routes/
│   └── reports.php
```

## Pending work

- Pruebas E2E: Create Customer → Create Loan → Register Payment → Verify Reports
- Add search/filter functionality by loan_number

## Frontend - Reports Module (2026-04-17)

**Reports Layout:**
- ✅ ReportsLayout.vue - sidebar con menú de navegación
- ✅ Routes anidadas para reportes

**Report Views:**
- ✅ SummaryReport.vue - Resumen general (KPIs principales)
- ✅ PortfolioReport.vue - Métricas de cartera
- ✅ ProfitabilityReport.vue - Rentabilidad con tabla detallada
- ✅ DelinquencyReport.vue - Mora con tabla y alertas
- ✅ ActiveLoansReport.vue - Tabla de préstamos activos
- ✅ CashFlowReport.vue - Flujo de caja con selector de fechas
- ✅ PaymentHistoryReport.vue - Historial de pagos
- ✅ AuditReport.vue - Registro de auditoría

**Layout:**
- ✅ ReportsLayout.vue - Overlay submenu con botón de cerrar
- ✅ Header con logo centrado y botón de menú
- ✅ Sidebar colapsable con toggle

**Rutas:**
```
/reportes → Resumen
/reportes/cartera → Cartera
/reportes/rentabilidad → Rentabilidad
/reportes/mora → Mora
/reportes/prestamos-activos → Préstamos Activos
/reportes/flujo-caja → Flujo de Caja
/reportes/historial-pagos → Historial de Pagos
/reportes/auditoria → Auditoría
```
