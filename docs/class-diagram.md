# Diagrama de Clases UML - Backend Laravel

## Descripcion General

Este documento presenta un diagrama de clases estilo UML para el sistema de gestion de prestamos (Loan Manager) desarrollado en Laravel. El sistema sigue una arquitectura orientada al dominio (DDD - Domain-Driven Design) con segregacion en contextos delimitados (Bounded Contexts):

- **LoanBC** (Loan Bounded Context): Gestion de prestamos
- **PaymentBC** (Payment Bounded Context): Gestion de pagos
- **CustomerBC** (Customer Bounded Context): Gestion de clientes
- **UserBC** (User Bounded Context): Gestion de usuarios
- **SharedKernel**: Valoros compartidos entre todos los contextos

---

## Convenciones de Relaciones

| Simbolo | Significado |
|---------|-------------|
| `→` | Asociacion (uso de otra clase) |
| `◄─` | Composicion (pertenencia fuerte) |
| `──►` | Dependencia (usa/depende de) |
| `──▷` | Herencia/Implementacion (extends/implements) |
| `--` | Relacion bidireccional |

---

## Indice de Contenidos

1. [Entidades del Dominio (Domain Entities)](#1-entidades-del-dominio-domain-entities)
2. [Objetos de Valor (Value Objects)](#2-objetos-de-valor-value-objects)
3. [DTOs y Responses](#3-dtos-y-responses)
4. [Controladores (Controllers)](#4-controladores-controllers)
5. [Interfaces de Repositorio](#5-interfaces-de-repositorio)
6. [Implementaciones de Repositorio](#6-implementaciones-de-repositorio)
7. [Diagrama de Relaciones entre Modulos](#7-diagrama-de-relaciones-entre-modulos)

---

## 1. Entidades del Dominio (Domain Entities)

### 1.1 Loan (Entidad de Prestamo)

**Namespace:** `App\LoanBC\Domain\Entities\Loan`

**Descripcion:** Entidad principal que representa un prestamo en el sistema. Maneja todo el ciclo de vida del prestamo incluyendo calculos de intereses, pagos y estados.

**Propiedades:**

| Atributo | Tipo | Visibilidad | Descripcion |
|----------|------|--------------|-------------|
| id | `LoanIdVO` | private readonly | Identificador unico del prestamo |
| customerId | `CustomerIdVO` | private readonly | ID del cliente asociados |
| originalCapital | `MoneyVO` | private readonly | Capital original solicitado |
| interestRate | `InterestRateVO` | private readonly | Tasa de interes aplicada |
| startDate | `DateVO` | private readonly | Fecha de inicio del prestamo |
| dueDate | `DateVO` | private readonly | Fecha de vencimiento |
| createdAt | `DateVO` | private readonly | Fecha de creacion |
| status | `LoanStatus` | private | Estado actual del prestamo |
| paidInterest | `MoneyVO` | private | Interes total pagado |
| paidCapital | `MoneyVO` | private | Capital total pagado |
| capital | `MoneyVO` | private | Capital actual pendiente |
| remainingDebt | `MoneyVO` | private | Deuda remanente |
| nextPaymentDate | `DateVO` | private | Proxima fecha de pago |

**Metodos:**

| Metodo | Parametros | Retorno | Descripcion |
|--------|------------|---------|-------------|
| `create()` | `CustomerIdVO $customerId, MoneyVO $capital, InterestRateVO $interestRate, DateVO $startDate, DateVO $dueDate` | `self` | Factory method para crear nuevo prestamo |
| `reconstitute()` | `LoanIdVO $id, CustomerIdVO $customerId, MoneyVO $originalCapital, InterestRateVO $interestRate, DateVO $startDate, DateVO $dueDate, DateVO $createdAt, LoanStatus $status, MoneyVO $paidInterest, MoneyVO $paidCapital, MoneyVO $capital, MoneyVO $remainingDebt, DateVO $nextPaymentDate` | `self` | Factory method para reconstruir entidad desde BD |
| `makePayment()` | `MoneyVO $amount` | `self` | Procesa un pago al prestamo |
| `isInDefault()` | - | `bool` | Verifica si el prestamo esta en mora |
| `calculateCurrentInterest()` | - | `MoneyVO` | Calcula el interes actual |
| `getId()` | - | `LoanIdVO` | Getter del ID |
| `getCustomerId()` | - | `CustomerIdVO` | Getter del ID del cliente |
| `getOriginalCapital()` | - | `MoneyVO` | Getter del capital original |
| `getCapital()` | - | `MoneyVO` | Getter del capital actual |
| `getRemainingDebt()` | - | `MoneyVO` | Getter de la deuda remanente |
| `getInterestRate()` | - | `InterestRateVO` | Getter de la tasa de interes |
| `getStartDate()` | - | `DateVO` | Getter de fecha de inicio |
| `getDueDate()` | - | `DateVO` | Getter de fecha de vencimiento |
| `getStatus()` | - | `LoanStatus` | Getter del estado |
| `getPaidInterest()` | - | `MoneyVO` | Getter del interes pagado |
| `getPaidCapital()` | - | `MoneyVO` | Getter del capital pagado |
| `getNextPaymentDate()` | - | `DateVO` | Getter de proxima fecha de pago |
| `getCreatedAt()` | - | `DateVO` | Getter de fecha de creacion |

**Relaciones:**
```
Loan ──▷ LoanIdVO    (identificacion)
Loan ──▷ CustomerIdVO (asociacion)
Loan ──▷ MoneyVO     (usa para capital, intereses)
Loan ──▷ InterestRateVO (calcula intereses)
Loan ──▷ DateVO       (manejo de fechas)
Loan ──▷ LoanStatus  (estado del prestamo)
Loan ──► DomainException (lanza excepciones)
```

---

### 1.2 Payment (Entidad de Pago)

**Namespace:** `App\PaymentBC\Domain\Entities\Payment`

**Descripcion:** Entidad que representa un pago realizado a un prestamo. Maneja el ciclo de vida del pago (pendiente -> validado -> aplicado).

**Propiedades:**

| Atributo | Tipo | Visibilidad | Descripcion |
|----------|------|--------------|-------------|
| id | `PaymentIdVO` | private readonly | Identificador unico del pago |
| loanId | `LoanIdVO` | private readonly | ID del prestamo asociado |
| amount | `MoneyVO` | private readonly | Monto del pago |
| paymentDate | `DateVO` | private readonly | Fecha del pago |
| createdAt | `DateVO` | private readonly | Fecha de creacion |
| interestPaid | `MoneyVO` | private | Interes cubierto por el pago |
| capitalPaid | `MoneyVO` | private | Capital cubierto por el pago |
| status | `PaymentStatus` | private | Estado del pago |

**Metodos:**

| Metodo | Parametros | Retorno | Descripcion |
|--------|------------|---------|-------------|
| `create()` | `LoanIdVO $loanId, MoneyVO $amount, ?DateVO $paymentDate` | `self` | Factory method para crear nuevo pago |
| `reconstitute()` | `PaymentIdVO $id, LoanIdVO $loanId, MoneyVO $amount, DateVO $paymentDate, DateVO $createdAt, PaymentStatus $status, ?MoneyVO $interestPaid, ?MoneyVO $capitalPaid` | `self` | Reconstruye entidad desde BD |
| `validate()` | - | `self` | Valida el pago (cambia a VALIDATED) |
| `apply()` | `MoneyVO $interestPortion, MoneyVO $capitalPortion` | `self` | Aplica el pago al prestamo |
| `reject()` | `string $reason` | `self` | Rechaza el pago |
| `getId()` | - | `PaymentIdVO` | Getter del ID |
| `getLoanId()` | - | `LoanIdVO` | Getter del ID del prestamo |
| `getAmount()` | - | `MoneyVO` | Getter del monto |
| `getPaymentDate()` | - | `DateVO` | Getter de fecha de pago |
| `getCreatedAt()` | - | `DateVO` | Getter de fecha de creacion |
| `getStatus()` | - | `PaymentStatus` | Getter del estado |
| `getInterestPaid()` | - | `?MoneyVO` | Getter del interes pagado |
| `getCapitalPaid()` | - | `?MoneyVO` | Getter del capital pagado |

**Relaciones:**
```
Payment ──▷ PaymentIdVO (identificacion)
Payment ──▷ LoanIdVO    (asociacion con prestamo)
Payment ──▷ MoneyVO    (manejo de montos)
Payment ──▷ DateVO     (fechas)
Payment ──▷ PaymentStatus (estado)
Payment ──► DomainException (excepciones)
```

---

### 1.3 Customer (Entidad de Cliente)

**Namespace:** `App\CustomerBC\Domain\Entities\Customer`

**Descripcion:** Entidad que representa un cliente del sistema de prestamos.

**Propiedades:**

| Atributo | Tipo | Visibilidad | Descripcion |
|----------|------|--------------|-------------|
| id | `CustomerIdVO` | private readonly | Identificador unico |
| personalData | `PersonVO` | private readonly | Datos personales del cliente |
| createdAt | `DateVO` | private readonly | Fecha de creacion |
| enabled | `bool` | private readonly | Indica si el cliente esta activo |

**Metodos:**

| Metodo | Parametros | Retorno | Descripcion |
|--------|------------|---------|-------------|
| `create()` | `PersonVO $personalData, ?DateVO $createdAt` | `self` | Factory method para crear cliente |
| `reconstitute()` | `CustomerIdVO $id, PersonVO $personalData, DateVO $createdAt, bool $enabled` | `self` | Reconstruye cliente desde BD |
| `getId()` | - | `CustomerIdVO` | Getter del ID |
| `getPersonalData()` | - | `PersonVO` | Getter de datos personales |
| `getCreatedAt()` | - | `DateVO` | Getter de fecha de creacion |
| `isEnabled()` | - | `bool` | Verifica si cliente esta activo |
| `disable()` | - | `self` | Deshabilita el cliente |
| `enable()` | - | `self` | Habilita el cliente |

**Relaciones:**
```
Customer ──▷ CustomerIdVO (identificacion)
Customer ──▷ PersonVO    (datos personales)
Customer ──▷ DateVO     (fechas)
```

---

### 1.4 User (Entidad de Usuario)

**Namespace:** `App\UserBC\Domain\Entities\User`

**Descripcion:** Entidad que representa un usuario del sistema (para autenticacion).

**Propiedades:**

| Atributo | Tipo | Visibilidad | Descripcion |
|----------|------|--------------|-------------|
| id | `UserIdVO` | private readonly | Identificador unico |
| personalData | `PersonVO` | private readonly | Datos personales |
| password | `string` | private readonly | Contrasena hasheada |
| rememberToken | `?string` | private readonly | Token de remember me |
| emailVerifiedAt | `?DateVO` | private readonly | Fecha de verificacion de email |
| createdAt | `DateVO` | private readonly | Fecha de creacion |
| enabled | `bool` | private readonly | Cuenta habilitada |

**Metodos:**

| Metodo | Parametros | Retorno | Descripcion |
|--------|------------|---------|-------------|
| `create()` | `PersonVO $personalData, string $password` | `self` | Factory method para crear usuario |
| `reconstitute()` | `UserIdVO $id, PersonVO $personalData, string $password, ?string $rememberToken, ?DateVO $emailVerifiedAt, DateVO $createdAt, bool $enabled` | `self` | Reconstruye usuario desde BD |
| `verifyPassword()` | `string $password` | `bool` | Verifica contrasena |
| `setRememberToken()` | `string $token` | `self` | Establece token de remember |
| `enable()` | - | `self` | Habilita usuario |
| `disable()` | - | `self` | Deshabilita usuario |
| `getId()` | - | `UserIdVO` | Getter del ID |
| `getPersonalData()` | - | `PersonVO` | Getter de datos personales |
| `getPassword()` | - | `string` | Getter de contrasena |
| `getRememberToken()` | - | `?string` | Getter del token |
| `getEmailVerifiedAt()` | - | `?DateVO` | Getter de verificacion de email |
| `getCreatedAt()` | - | `DateVO` | Getter de creacion |
| `isEnabled()` | - | `bool` | Verifica si esta habilitado |
| `isVerified()` | - | `bool` | Verifica si email esta verificado |

**Relaciones:**
```
User ──▷ UserIdVO     (identificacion)
User ──▷ PersonVO    (datos personales)
User ──▷ DateVO     (fechas)
User ──► DomainException (valida contrasena)
```

---

## 2. Objetos de Valor (Value Objects)

### 2.1 MoneyVO

**Namespace:** `App\SharedKernel\Domain\ValueObjects\MoneyVO`

**Descripcion:** Objeto de valor inmutable que representa dinero con tipo de moneda.

**Propiedades:**

| Atributo | Tipo | Visibilidad |
|----------|------|--------------|
| amount | `int` | private readonly |
| currency | `Currency` | private readonly |

**Metodos:**

| Metodo | Parametros | Retorno |
|--------|------------|--------|
| `create()` | `int $amount, Currency $currency` | `self` |
| `zero()` | `?Currency $currency` | `self` |
| `getAmount()` | - | `int` |
| `getCurrency()` | - | `Currency` |
| `getFormatted()` | - | `string` |
| `add()` | `self $other` | `self` |
| `subtract()` | `self $other` | `self` |
| `multiply()` | `int $factor` | `self` |
| `isZero()` | - | `bool` |
| `isGreaterThan()` | `self $other` | `bool` |
| `isLessThan()` | `self $other` | `bool` |
| `equals()` | `self $other` | `bool` |

**Relaciones:**
```
MoneyVO ◄─ Currency (enum - moneda)
MoneyVO ──► InvalidMoneyException
```

---

### 2.2 DateVO

**Namespace:** `App\SharedKernel\Domain\ValueObjects\DateVO`

**Descripcion:** Objeto de valor inmutable para manejo de fechas.

**Propiedades:**

| Atributo | Tipo | Visibilidad |
|----------|------|--------------|
| date | `DateTimeInterface` | private readonly |

**Metodos:**

| Metodo | Parametros | Retorno |
|--------|------------|--------|
| `create()` | `DateTimeInterface\|string $date` | `self` |
| `today()` | - | `self` |
| `now()` | - | `self` |
| `getDate()` | - | `DateTimeInterface` |
| `getYear()` | - | `int` |
| `getMonth()` | - | `int` |
| `getDay()` | - | `int` |
| `getFormatted()` | `?string $format` | `string` |
| `isAfter()` | `self $other` | `bool` |
| `isBefore()` | `self $other` | `bool` |
| `isSameDay()` | `self $other` | `bool` |
| `diffInDays()` | `self $other` | `int` |
| `addMonths()` | `int $months` | `self` |
| `isFuture()` | - | `bool` |
| `isPast()` | - | `bool` |
| `equals()` | `self $other` | `bool` |

---

### 2.3 NameVO

**Namespace:** `App\SharedKernel\Domain\ValueObjects\NameVO`

**Descripcion:** Objeto de valor para nombres completos con validacion.

**Propiedades:**

| Atributo | Tipo | Visibilidad |
|----------|------|--------------|
| firstName | `string` | private readonly |
| lastName | `string` | private readonly |
| middleName | `?string` | private readonly |
| secondLastName | `string` | private readonly |

**Metodos:**

| Metodo | Parametros | Retorno |
|--------|------------|--------|
| `create()` | `string $firstName, string $lastName, string $secondLastName, ?string $middleName` | `self` |
| `getFirstName()` | - | `string` |
| `getLastName()` | - | `string` |
| `getMiddleName()` | - | `?string` |
| `getSecondLastName()` | - | `string` |
| `getFullName()` | - | `string` |
| `getShortName()` | - | `string` |
| `equals()` | `self $other` | `bool` |

**Relaciones:**
```
NameVO ──► InvalidNameException
```

---

### 2.4 DniVO

**Namespace:** `App\SharedKernel\Domain\ValueObjects\DniVO`

**Descripcion:** Objeto de valor para documentos de identificacion (DNI, CC, NIT, etc).

**Propiedades:**

| Atributo | Tipo | Visibilidad |
|----------|------|--------------|
| number | `string` | private readonly |
| type | `DniType` | private readonly |

**Metodos:**

| Metodo | Parametros | Retorno |
|--------|------------|--------|
| `create()` | `string $number, DniType $type` | `self` |
| `getNumber()` | - | `string` |
| `getType()` | - | `DniType` |
| `getFormatted()` | - | `string` |
| `equals()` | `self $other` | `bool` |

**Relaciones:**
```
DniVO ◄─ DniType (enum - tipo de documento)
DniVO ──► InvalidDniException
```

---

### 2.5 InterestRateVO

**Namespace:** `App\LoanBC\Domain\ValueObjects\InterestRateVO`

**Descripcion:** Objeto de valor para tasas de interes (anual y mensual).

**Propiedades:**

| Atributo | Tipo | Visibilidad |
|----------|------|--------------|
| annualRate | `float` | private readonly |
| monthlyRate | `float` | private readonly |

**Metodos:**

| Metodo | Parametros | Retorno |
|--------|------------|--------|
| `createAnnual()` | `float $annualRate` | `self` |
| `createMonthly()` | `float $monthlyRate` | `self` |
| `getAnnualRate()` | - | `float` |
| `getMonthlyRate()` | - | `float` |
| `calculateInterest()` | `MoneyVO $capital` | `MoneyVO` |
| `calculateMonthlyInterestFromCapital()` | `int $capitalAmount` | `int` |
| `equals()` | `self $other` | `bool` |

**Relaciones:**
```
InterestRateVO ──▷ MoneyVO (calcula intereses)
InterestRateVO ──► BusinessRuleViolationException
```

---

### 2.6 LoanStatus (Enum)

**Namespace:** `App\LoanBC\Domain\ValueObjects\LoanStatus`

**Descripcion:** Enum que representa los estados posibles de un prestamo.

```
enum LoanStatus: string
├── ACTIVE    = 'active'     (prestamo activo)
├── PAID     = 'paid'       (prestamo pagado)
├── DEFAULTED = 'defaulted' (en mora)
└── CANCELLED = 'cancelled' (cancelado)
```

---

### 2.7 PaymentStatus (Enum)

**Namespace:** `App\PaymentBC\Domain\ValueObjects\PaymentStatus`

**Descripcion:** Enum que representa los estados de un pago.

```
enum PaymentStatus: string
├── PENDING   = 'pending'   (pendiente)
├── VALIDATED = 'validated' (validado)
├── APPLIED   = 'applied'   (aplicado al prestamo)
├── REJECTED  = 'rejected'  (rechazado)
└── REFUNDED  = 'refunded'  (reembolsado)
```

---

### 2.8 Objetos de Valor de Identificacion (ID Value Objects)

#### LoanIdVO

**Namespace:** `App\LoanBC\Domain\ValueObjects\LoanIdVO`

| Metodo | Parametros | Retorno |
|--------|------------|--------|
| `fromString()` | `string $value` | `static` |
| `generate()` | - | `static` |
| `getValue()` | - | `string` |
| `equals()` | `self $other` | `bool` |

#### PaymentIdVO

**Namespace:** `App\PaymentBC\Domain\ValueObjects\PaymentIdVO`

Igual estructura que LoanIdVO.

#### CustomerIdVO

**Namespace:** `App\CustomerBC\Domain\ValueObjects\CustomerIdVO`

Igual estructura que LoanIdVO.

---

## 3. DTOs y Responses

### 3.1 LoanResponse

**Namespace:** `App\LoanBC\Application\DTOs\LoanResponse`

**Descripcion:** DTO para transferencia de datos de prestamo a la capa de presentacion.

**Propiedades:**

| Atributo | Tipo |
|----------|------|
| id | `string` |
| customerId | `string` |
| capital | `array` |
| remainingDebt | `array` |
| interestRate | `array` |
| startDate | `string` |
| dueDate | `string` |
| nextPaymentDate | `string` |
| status | `string` |
| paidCapital | `array` |
| paidInterest | `array` |
| createdAt | `string` |

**Metodos:**

| Metodo | Parametros | Retorno |
|--------|------------|--------|
| `fromEntity()` | `Loan $loan` | `self` |
| `toArray()` | - | `array` |

**Relaciones:**
```
LoanResponse ──► Loan (transforma entidad a DTO)
LoanResponse ──▷ MoneyVO (convierte a array)
```

---

### 3.2 PaymentResponse

**Namespace:** `App\PaymentBC\Application\DTOs\PaymentResponse`

**Descripcion:** DTO para transferencia de datos de pago a la capa de presentacion.

**Propiedades:**

| Atributo | Tipo |
|----------|------|
| id | `string` |
| loanId | `string` |
| amount | `array` |
| paymentDate | `string` |
| status | `string` |
| interestPaid | `?array` |
| capitalPaid | `?array` |
| createdAt | `string` |

**Metodos:**

| Metodo | Parametros | Retorno |
|--------|------------|--------|
| `fromEntity()` | `Payment $payment` | `self` |
| `toArray()` | - | `array` |

**Relaciones:**
```
PaymentResponse ──► Payment (transforma entidad a DTO)
PaymentResponse ──▷ MoneyVO (convierte a array)
```

---

## 4. Controladores (Controllers)

### 4.1 LoanController

**Namespace:** `App\LoanBC\Presentation\Controllers\LoanController`

**Descripcion:** Controlador para manejar las peticiones HTTP relacionadas con prestamos.

**Dependencias Inyectadas:**

| Servicio | Tipo |
|----------|------|
| createLoanUseCase | `CreateLoanUseCase` |
| getLoanByIdUseCase | `GetLoanByIdUseCase` |
| getAllLoansUseCase | `GetAllLoansUseCase` |
| getLoanReportUseCase | `GetLoanReportUseCase` |
| makePaymentUseCase | `MakePaymentUseCase` |

**Metodos:**

| Metodo | Parametros | Retorno | Descripcion |
|--------|------------|---------|---------|-------------|
| `store()` | `Request $request` | `JsonResponse` | Crea un nuevo prestamo |
| `show()` | `string $id` | `JsonResponse` | Muestra un prestamo por ID |
| `index()` | - | `JsonResponse` | Lista todos los prestamos |
| `report()` | - | `JsonResponse` | Genera reporte de prestamos |
| `makePayment()` | `Request $request, string $id` | `JsonResponse` | Realiza un pago |

**Relaciones:**
```
LoanController ──► CreateLoanCommand
LoanController ──► MakePaymentCommand
LoanController ──► InterestRateVO
LoanController ──► MoneyVO
LoanController ──► DateVO
LoanController ──► LoanIdVO
LoanController ──► CustomerIdVO
```

---

### 4.2 PaymentController

**Namespace:** `App\PaymentBC\Presentation\Controllers\PaymentController`

**Descripcion:** Controlador para manejar las peticiones HTTP relacionadas con pagos.

**Dependencias Inyectadas:**

| Servicio | Tipo |
|----------|------|
| processPaymentUseCase | `ProcessPaymentUseCase` |

**Metodos:**

| Metodo | Parametros | Retorno | Descripcion |
|--------|------------|---------|---------|-------------|
| `store()` | `Request $request` | `JsonResponse` | Procesa un nuevo pago |
| `index()` | - | `JsonResponse` | Lista todos los pagos |

**Relaciones:**
```
PaymentController ──► ProcessPaymentCommand
PaymentController ──► ProcessPaymentUseCase
PaymentController ──► PaymentModel (Eloquent)
```

---

### 4.3 CustomerController

**Namespace:** `App\CustomerBC\Presentation\Controllers\CustomerController`

**Descripcion:** Controlador para manejar las peticiones HTTP relacionadas con clientes.

**Dependencias Inyectadas:**

| Servicio | Tipo |
|----------|------|
| createMapper | `CreateCustomerRequestMapper` |
| updateMapper | `UpdateCustomerRequestMapper` |
| createUseCase | `CreateCustomerUseCase` |
| getByIdUseCase | `GetCustomerByIdUseCase` |
| getAllUseCase | `GetAllCustomersUseCase` |
| getAllSummaryUseCase | `GetAllCustomersSummaryUseCase` |
| updateUseCase | `UpdateCustomerUseCase` |
| reportUseCase | `GetCustomerReportUseCase` |

**Metodos:**

| Metodo | Parametros | Retorno | Descripcion |
|--------|------------|---------|---------|-------------|
| `store()` | `Request $request` | `JsonResponse` | Crea un nuevo cliente |
| `show()` | `string $id` | `JsonResponse` | Muestra cliente por ID |
| `index()` | - | `JsonResponse` | Lista todos los clientes |
| `summary()` | - | `JsonResponse` | Resumen de clientes |
| `report()` | - | `JsonResponse` | Genera reporte |
| `update()` | `Request $request, string $id` | `JsonResponse` | Actualiza cliente |

**Relaciones:**
```
CustomerController ──► DTOs (CustomerResponse, CustomerSummaryResponse)
CustomerController ──► UseCases
CustomerController ──► Mappers
```

---

## 5. Interfaces de Repositorio

### 5.1 Interfaces de Loan (LoanBC)

#### LoanFinderById

```php
interface LoanFinderById {
    public function findById(LoanIdVO $id): ?Loan;
}
```

#### LoanFinderAll

```php
interface LoanFinderAll {
    public function findAll(): array;
}
```

#### LoanFinderByCustomerId

```php
interface LoanFinderByCustomerId {
    public function findByCustomerId(CustomerIdVO $customerId): array;
}
```

#### LoanCreator

```php
interface LoanCreator {
    public function create(Loan $loan): void;
}
```

#### LoanUpdater

```php
interface LoanUpdater {
    public function update(Loan $loan): void;
}
```

---

### 5.2 Interfaces de Payment (PaymentBC)

#### PaymentFinderById

```php
interface PaymentFinderById {
    public function findById(PaymentIdVO $id): ?Payment;
}
```

#### PaymentFinderByLoanId

```php
interface PaymentFinderByLoanId {
    public function findByLoanId(LoanIdVO $loanId): array;
}
```

#### PaymentCreator

```php
interface PaymentCreator {
    public function create(Payment $payment): void;
}
```

---

## 6. Implementaciones de Repositorio

### 6.1 Implementaciones de Loan (Eloquent)

Las implementaciones siguen el patron Repository usando Eloquent de Laravel:

| Interfaz | Implementacion |
|----------|----------------|
| LoanFinderById | `EloquentLoanFinderById` |
| LoanFinderAll | `EloquentLoanFinderAll` |
| LoanFinderByCustomerId | `EloquentLoanFinderByCustomerId` |
| LoanCreator | `EloquentLoanCreator` |
| LoanUpdater | `EloquentLoanUpdater` |

**Ejemplo - EloquentLoanCreator:**

```php
final class EloquentLoanCreator implements LoanCreator {
    public function create(Loan $loan): void {
        // Implementacion con Eloquent
    }
}
```

---

## 7. Diagrama de Relaciones entre Modulos

### 7.1 Diagrama General de Arquitectura

```
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│                            PRESENTATION LAYER                                   │
├─────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐         │
│  │ LoanController  │  │PaymentController│  │CustomerController│         ���
│  └────────┬────────┘  └────────┬────────┘  └────────┬────────┘         │
│           │                    │                    │                     │
│           └────────────────────┼────────────────────┘                     │
│                              ▼                                              │
│                    ┌─────────────────┐                                    │
│                    │  Use Cases      │◄──────── Dependencia                 │
│                    └────────┬───────┘                                    │
└───────────────────────────────┼─────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│                            APPLICATION LAYER                                  │
├─────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐         │
│  │CreateLoanCommand│  │MakePaymentCommand│  │ProcessPaymentCmd │         │
│  └────────┬───────┘  └────────┬───────┘  └────────┬───────┘         │
│           │                    │                    │                     │
│           └────────────────┬┴─────────────────┘                     │
│                              ▼                                              │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐         │
│  │ LoanResponse   │  │PaymentResponse │  │CustomerResponse│         │
│  └───────┬───────┘  └───────┬───────┘  └───────┬───────┘         │
└─────────┼─────────────────┼─────────────────┼───────────────────────┘
          │                 │                 │
          ▼                 ▼                 ▼
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                         DOMAIN LAYER                                            │
├─────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                   │
│  ┌─────────────────────────────┐    ┌─────────────────────────────┐              │
│  │      LOAN BC                 │    │      PAYMENT BC             │              │
│  │                             │    │                             │              │
│  │  ┌───────┐                  │    │  ┌───────┐                  │              │
│  │  │ Loan │◄──────────────┐   │    │  │Pay-  │◄─────────┐     │              │
│  │  └──────┘              │   │    │  │ment  │          │     │              │
│  │       │                │   │    │  └──────┘          │     │              │
│  │       ▼                │   │    │       │              │     │              │
│  │  ┌────────────┐       │   │    │       ▼              │     │              │
│  │  │ LoanIdVO   │       │   │    │  ┌────────┐        │     │              │
│  │  └────────────┘       │   │    │  │Payment │        │     │              │
│  │  ┌────────────┐       │   │    │  │IdVO    │        │     │              │
│  │  │LoanStatus │       │   │    │  └────────┘        │     │              │
│  │  │  (enum)   │       │   │    │  ┌────────┐        │     │              │
│  │  └────────────┘       │   │    │  │Payment │        │     │              │
│  │  ┌────────────┐       │   │    │  │Status  │        │     │              │
│  │  │Interest-  │       │   │    │  │ (enum) │        │     │              │
│  │  │RateVO     │       │   │    │  └────────┘        │     │              │
│  │  └────────────┘       │   │    └───────────────────│────────┘       │              │
│  └────────────────────────│──│───────────────────│─────────────────┘              │
│                          │  │                                               │
│        ◄────────────────┘  └─────────────────►                            │
│                              │                                                │
│  ┌─────────────────────────────────────────────────┐                        │
│  │              SHARED KERNEL                      │                        │
│  │                                                │                        │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐      │                        │
│  │  │MoneyVO   │  │ DateVO   │  │ NameVO   │      │                        │
│  │  └──────────┘  └──────────┘  └──────────┘      │                        │
│  │  ┌──────────┐  ┌──────────��  ┌──────────┐      │                        │
│  │  │ DniVO    │  │PersonVO  │  │ EmailVO  │      │                        │
│  │  └──────────┘  └──────────┘  └──────────┘      │                        │
│  │  ┌──────────┐  ┌──────────┐                      │                        │
│  │  │ PhoneVO  │  │ AddressVO│                      │                        │
│  │  └──────────┘  └──────────┘                      │                        │
│  └─────────────────────────────────────────────────┘                        │
└───────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌───────────────────────────────────────────────────────────────────────────────────────┐
│                    INFRASTRUCTURE LAYER                                      │
├───────────────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  ┌─────────────────────────────────────────────────────┐              │
│  │            ELOQUENT REPOSITORIES                    │              │
│  │                                                │   │
│  │  ┌───────────┐ ┌───────────┐ ┌────────────┐    │   │
│  │  │Eloquent- │ │Eloquent- │ │Eloquent-  │    │   │
│  │  │LoanFound- │ │Loan-     │ │LoanFinder- │    │   │
│  │  │erById     │ │Creator   │ │ByCustomer │    │   │
│  │  └───────────┘ └───────────┘ └────────────┘    │   │
│  └─────────────────────────────────────────────────────┘              │
│                                                                       │
└───────────────────────────────────────────────────────────────────────┘
```

### 7.2 Flujo de Datos para Creacion de Prestamo

```
HTTP Request (store)
       │
       ▼
LoanController::store()
       │
       ▼
CreateLoanCommand
       │
       ▼
CreateLoanUseCase
       │
       ├──────────────────────────────┐
       │                              │
       ▼                              ▼
LoanCreator         LoanFinderById
       │              │
       │              │
       └──────────────┘
              │
              ▼
        Loan Entity
              │
              ├──────────┬─────────────┬───────────┐
              ▼          ▼             ▼           ▼
         MoneyVO    InterestRateVO  DateVO   CustomerIdVO
              │                             │
              └─────────────────────────────┘
                           │
                           ▼
                    LoanResponse
                           │
                           ▼
                    JSON Response
```

### 7.3 Flujo de Datos para Realizar Pago

```
HTTP Request (makePayment)
       │
       ▼
LoanController::makePayment()
       │
       ▼
MakePaymentCommand
       │
       ▼
MakePaymentUseCase
       │
       ├──────────────────────────┐
       │                          │
       ▼                          ▼
LoanFinderById             PaymentCreator
       │                          │
       ▼                          ▼
  Loan Entity            Payment Entity
       │                          │
       │    ┌────────────┐         │
       └────│ makePayment│◄────────┘
            │  (method)  │
            └────────────┘
                 │
                 ▼
           Actualiza Loan
           (new instance)
                 │
                 ▼
          LoanUpdater
          (persist)
                 │
                 ▼
          Payment applied
```

### 7.4 Relaciones entre Entidades

```
Customer                                 User
    │                                      │
    ├──────┐                               │
    │      │                               │
    ▼      ▼                               ▼
┌────────────────────────┐      ┌─────────────────┐
│     Loan               │      │   Auth System   │
│                        │      │                 │
│  ┌────────────────┐   │      └─────────────────┘
│  │ - customerId   │◄─┼───────► ( FK )
│  │ - capital      │   │
│  │ - interestRate │   │
│  │ - status       │   │
│  └────────────────┘   │
└───────────┬────────────┘
            │
            │ 1:N
            ▼
┌────────────────────────┐
│     Payment            │
│                        │
│  - loanId (FK)         │
│  - amount              │
│  - status              │
│  - interestPaid        │
│  - capitalPaid        │
└────────────────────────┘
```

---

## 8. Resumen de Patrones Aplicados

| Patron | Aplicacion |
|--------|-----------|
| **Factory Method** | `create()` y `reconstitute()` en todas las entidades |
| **Value Object** | Todos los VO son inmutables |
| **Repository** | Interfaces en Domain, implementaciones en Infrastructure |
| **CQRS** | Comandos y UseCases separados |
| **DTO** | Response classes para transferencia de datos |
| **Strategy** | Diferentes calculos de interes (InterestRateVO) |
| **Specification** | Validaciones en Value Objects |

---

## 9. Glosario de Terminos

| Termino | Definicion |
|--------|------------|
| **Bounded Context** | Limite delimitado que define un subdominio en DDD |
| **Value Object** | Objeto inmutable quedescribe un valor sin identidad |
| **Entity** | Objeto con identidad unica que cambia a lo largo del tiempo |
| **Repository** | Patron para acceso a persistencia |
| **Use Case** | Caso de uso que orquesta la logica de negocio |
| **Command** | Objeto que representa una accion a ejecutar |
| **DTO** | Data Transfer Object para transferir datos entre capas |
| **VO** | Value Object |

---

## 10. Referencias de archivos

### Entidades

- `/app/LoanBC/Domain/Entities/Loan.php`
- `/app/PaymentBC/Domain/Entities/Payment.php`
- `/app/CustomerBC/Domain/Entities/Customer.php`
- `/app/UserBC/Domain/Entities/User.php`

### Value Objects

- `/app/SharedKernel/Domain/ValueObjects/MoneyVO.php`
- `/app/SharedKernel/Domain/ValueObjects/DateVO.php`
- `/app/SharedKernel/Domain/ValueObjects/NameVO.php`
- `/app/SharedKernel/Domain/ValueObjects/DniVO.php`
- `/app/LoanBC/Domain/ValueObjects/InterestRateVO.php`
- `/app/LoanBC/Domain/ValueObjects/LoanStatus.php`
- `/app/LoanBC/Domain/ValueObjects/LoanIdVO.php`
- `/app/PaymentBC/Domain/ValueObjects/PaymentStatus.php`
- `/app/PaymentBC/Domain/ValueObjects/PaymentIdVO.php`
- `/app/CustomerBC/Domain/ValueObjects/CustomerIdVO.php`

### DTOs

- `/app/LoanBC/Application/DTOs/LoanResponse.php`
- `/app/PaymentBC/Application/DTOs/PaymentResponse.php`

### Controladores

- `/app/LoanBC/Presentation/Controllers/LoanController.php`
- `/app/PaymentBC/Presentation/Controllers/PaymentController.php`
- `/app/CustomerBC/Presentation/Controllers/CustomerController.php`

### Repositorios

- `/app/LoanBC/Domain/Repositories/*.php`
- `/app/PaymentBC/Domain/Repositories/*.php`
- `/app/LoanBC/Infrastructure/Repository/*.php`

---

*Documento generado automaticamente a partir del codigo fuente del proyecto Laravel Loan Manager*
*Fecha de generacion: 2026-04-11*
