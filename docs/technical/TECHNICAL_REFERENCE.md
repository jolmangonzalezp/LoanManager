# Technical Reference: LoanManager

## 1. Architectural Overview
LoanManager is built using **Domain-Driven Design (DDD)** and **Hexagonal Architecture** (Ports and Adapters). This ensures that the core business logic is decoupled from external frameworks, databases, and APIs.

### 1.1. Layers
- **Domain:** The core of the system. Contains Aggregates, Entities, Value Objects, and Domain Events. Defines Repository interfaces (Ports).
- **Application:** Orchestrates use cases. Handles CQRS (Command/Query Responsibility Segregation).
- **Infrastructure:** Implements Ports (Adapters). Contains Eloquent models, Mappers, and external service integrations.
- **Presentation:** Controllers and Presenters that handle HTTP requests and format responses.

## 2. Bounded Contexts (BC)

### 2.1. CustomerBC
- **Core Entity:** `Customer`
- **Use Cases:** Register Customer, Update Profile, List Customers.
- **Key Logic:** DNI/ID validation and hashing for privacy.

### 2.2. LoanBC
- **Core Entity:** `Loan`
- **Aggregates:** `LoanType`
- **Value Objects:** `InterestRateVO` (handles monthly/annual calculations).
- **Use Cases:** Originate Loan, Calculate Amortization, Update Loan Status.
- **Business Logic:** Logic for interest accrual and loan state transitions (`active` -> `paid` -> `defaulted`).

### 2.3. PaymentBC
- **Core Entity:** `Payment`
- **Use Cases:** Register Payment, Process Installment, Monthly Collection Report.
- **Business Logic:** The system strictly follows a "Prioritize Interest" rule for payment distribution.

### 2.4. RouteBC
- **Core Entities:** `Zone`, `Route`
- **Use Cases:** Create Zone, Define Route, Assign Collectors to Routes.
- **Logic:** Spatial organization of customers using GeoJSON for optimized collection routes.

### 2.5. ReportBC
- **Logic:** Aggregated queries for Portfolio Health, Profitability, and Cash Flow.

### 2.6. UserBC
- **Core Entity:** `User`
- **Auth:** Laravel Sanctum for API token management.
- **Security:** RBAC (Role-Based Access Control) with Roles and Permissions.

### 2.7. SharedKernel
- **Value Objects:** `MoneyVO` (prevents floating point errors), `DateVO`, `UuidVO`, `AddressVO`.

## 3. Database Schema
The system uses a relational schema (MariaDB) with the following key tables:
- `users`: Authenticated users with roles.
- `customers`: Client personal data and identification.
- `loans`: Financial data (capital, remaining debt, interest rates).
- `payments`: Transaction history and distribution logs.
- `zones` / `routes`: Spatial boundaries and organizational paths.
- `loan_types`: Configuration for different loan products.

## 4. Core Business Logic

### 4.1. Interest Calculation
Managed by `InterestRateVO`. 
- **Monthly Rate:** Used for periodic interest generation.
- **Formula:** `Remaining Capital * (Rate / 100)`.

### 4.2. Payment Distribution
Implemented in `Loan::makePayment`:
1. **Pay Interest First:** The amount is first applied to any unpaid interest for the current/past periods.
2. **Amortize Capital:** Any remaining funds are deducted from the `remaining_debt`.
3. **Status Update:** If `remaining_debt` reaches 0, the loan status is updated to `paid`.

## 5. API Reference
- **Base URL:** `/api`
- **Authentication:** Bearer Token (Sanctum).
- **Key Endpoints:**
    - `POST /auth/login`: Authentication.
    - `GET /customers`: List all customers.
    - `POST /loans`: Create a new loan.
    - `POST /loans/{id}/payment`: Process a payment for a specific loan.
    - `GET /reports/profitability`: Get financial metrics.

## 6. Frontend Architecture
- **Framework:** Vue 3 (Composition API) + TypeScript.
- **Structure:** Modularized by Bounded Context in `resources/js/Modules`.
- **UI Components:** Tailored components in `resources/js/Shared`.
- **State:** Lightweight global state using shared Composables.

## 7. Infrastructure & Deployment
- **Runtime:** PHP 8.4 + Apache.
- **Containers:** Dockerized environment via `Dockerfile` and `docker-compose`.
- **CI/CD:** Automated migrations and cache warming via `entrypoint.sh`.
- **Cloud:** Optimized for serverless deployment (Laravel Cloud).

---
*Technical Reference - v1.0.0*
