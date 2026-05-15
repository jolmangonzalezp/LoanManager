# Business Rules: LoanManager Operational Logic

This document specifies the core business rules and algorithms implemented in the LoanManager system.

## 1. Financial Rules

### 1.1. Payment Distribution Logic
When a payment is processed, the system follows a strict hierarchy to protect the business's profitability.

**Algorithm:**
1.  **Identify Interest Period:** The system checks if the current date matches the `interest_period` of the loan.
2.  **Accrue Monthly Interest:** If the period has changed (e.g., a new month), the system calculates the monthly interest based on the `remaining_debt`.
3.  **Interest-First Allocation:**
    -   The payment amount is first applied to the `pending_interest`.
    -   If the payment exceeds the `pending_interest`, the remainder is applied to the `paid_capital` and subtracted from the `remaining_debt`.
4.  **Balance Update:** The `remaining_debt` is updated immediately to reflect the new principal.

### 1.2. Interest Calculation
Managed by the `InterestRateVO`, interest is calculated monthly.

**Formula:**
`Monthly Interest = round(Remaining Capital * (Monthly Interest Rate / 100))`

-   **Precision:** Calculations are performed using integers (cents) to avoid floating-point errors.
-   **Rate Conversion:** Annual rates are converted to monthly rates by dividing by 12 (`Annual Rate / 12`).

### 1.3. Financial Precision (`MoneyVO`)
The system treats money as a discrete unit (integers).
-   **No Negatives:** `MoneyVO` cannot represent negative values. Operations that would result in a negative balance are floored at zero.
-   **Immutability:** Financial objects are immutable; any operation (`add`, `subtract`) returns a new instance.

## 2. Loan Lifecycle Rules

### 2.1. Loan State Machine
Loans transition between states based on financial events:
-   **`active`:** Initial state upon creation. Loan has a balance and is within terms.
-   **`paid`:** Transition occurs automatically when `remaining_debt` reaches exactly 0.
-   **`defaulted`:** Transition triggered manually or by automated jobs if the `due_date` is passed without full payment. In this state, `pending_interest` is often consolidated into the `remaining_debt`.

### 2.2. Loan Originator Rules
-   **Start Date:** If a loan is created with a future start date, the system defaults the "effective" start date to `now` for interest accrual purposes.
-   **Initial Interest:** The first month's interest is generated immediately upon creation based on the full capital.

## 3. Customer & Security Rules

### 3.1. Identification Integrity
-   **DNI/ID Uniqueness:** Customers are uniquely identified by their ID documents.
-   **Hashing:** Sensitive identification numbers are hashed in the database (Infrastructure layer) to maintain privacy while allowing for lookups.

### 3.2. Role-Based Access Control (RBAC)
Access to business operations is strictly gated by roles:
-   **Operator:** Can register customers and process payments.
-   **Manager:** Can originate loans and view reports.
-   **Admin:** Full access, including user management and system configuration.

## 4. Collection Route Rules

### 4.1. Spatial Assignment
-   **Zones:** Customers are grouped into geographic polygons (Zones).
-   **Routes:** A sequence of customers within a zone assigned to a specific collector.
-   **Assignment:** Only one active collector can be assigned to a route at any given time to prevent collection conflicts.

---
*Business Rules Documentation - v1.0.0*
