# Documentacion de Arquitectura del Backend Laravel

## Tabla de Contenidos

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Estructura del Proyecto](#2-estructura-del-proyecto)
3. [Rutas API](#3-rutas-api)
4. [Modulo CustomerBC - Gestion de Clientes](#4-modulo-customerbc---gestion-de-clientes)
5. [Modulo LoanBC - Gestion de Prestamos](#5-modulo-loanbc---gestion-de-prestamos)
6. [Modulo PaymentBC - Gestion de Pagos](#6-modulo-paymentbc---gestion-de-pagos)
7. [Modulo UserBC - Gestion de Usuarios](#7-modulo-userbc---gestion-de-usuarios)
8. [Modulo ReportBC - Reportes y Analisis](#8-modulo-reportbc---reportes-y-analisis)
9. [Modulo SharedKernel - Nucleo Compartido](#9-modulo-sharedkernel---nucleo-compartido)
10. [Esquema de Base de Datos](#10-esquema-de-base-de-datos)
11. [Patrones de Diseno Utilizados](#11-patrones-de-diseno-utilizados)

---

## 1. Resumen Ejecutivo

Este documento presenta la arquitectura detallada del sistema de gestion de prestamos (Loan Manager) desarrollado en Laravel. El proyecto sigue una arquitectura basada en **Bounded Contexts** (Contextos Delimitados) inspirada en **Domain-Driven Design (DDD)**, donde cada modulo representa un contexto de negocio independiente.

### Caracteristicas Principales

- **Arquitectura modular** con boundarys清晰地 definidos
- **Patron Repository** para acceso a datos
- **CQRS** (Command Query Responsibility Segregation) en la organizacion de UseCases
- **Value Objects** para representacion de conceptos del dominio
- **Cifrado de datos sensibles** en la base de datos

---

## 2. Estructura del Proyecto

```
Backend/
├── app/
│   ├── CustomerBC/          # Modulo de Gestion de Clientes
│   ├── LoanBC/              # Modulo de Gestion de Prestamos
│   ├── PaymentBC/           # Modulo de Gestion de Pagos
│   ├── UserBC/              # Modulo de Gestion de Usuarios
│   ├── ReportBC/            # Modulo de Reportes
│   └── SharedKernel/        # Nucleo Compartido
├── routes/
│   ├── api.php              # Rutas principales de API
│   └── reports.php          # Rutas de reportes
└── ...
```

### Capas dentro de cada Modulo

Cada modulo sigue una estructura hexagonal:

```
Modulo/
├── Application/             # Capa de Aplicacion
│   ├── Commands/            # Comandos (escritura)
│   ├── DTOs/                # Data Transfer Objects
│   ├── Exceptions/          # Excepciones especificas
│   ├── UseCases/            # Casos de uso
│   └── Config/              # Configuracion de servicios
├── Domain/                  # Capa de Dominio
│   ├── Entities/            # Entidades del negocio
│   ├── ValueObjects/        # Objetos de valor
│   ├── Repositories/        # Interfaces de repositorio
│   ├── Services/            # Servicios de dominio
│   └── Ports/               # Puertos (interfaces)
├── Infrastructure/          # Capa de Infraestructura
│   ├── Models/              # Modelos Eloquent
│   ├── Migrations/          # Migraciones de BD
│   ├── Persistence/         # Mapeadores y repositorios
│   ├── Repositories/        # Implementaciones Eloquent
│   └── Services/            # Servicios de infraestructura
└── Presentation/            # Capa de Presentacion
    ├── Controllers/         # Controladores HTTP
    ├── Mappers/             # Mapeadores de request/response
    └── Exceptions/          # Manejo de excepciones
```

---

## 3. Rutas API

### Archivo: `routes/api.php`

```php
Route::middleware(['handle.exceptions', 'handle.cors'])->group(function () {
    
    // Autenticacion
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    
    // Clientes
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/summary', [CustomerController::class, 'summary']);
    Route::get('/customers/report', [CustomerController::class, 'report']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    
    // Prestamos
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/report', [LoanController::class, 'report']);
    Route::get('/loans/{id}', [LoanController::class, 'show']);
    
    // Pagos
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments', [PaymentController::class, 'index']);
});
```

### Archivo: `routes/reports.php`

```php
Route::middleware('handle.exceptions')->group(function () {
    Route::get('/reports/projected-vs-actual', ...);
    Route::get('/reports/collection-availability', ...);
    Route::get('/reports/client-profitability', ...);
});
```

### Resumen de Endpoints

| Metodo | Endpoint | Descripcion |
|--------|----------|-------------|
| POST | /auth/login | Iniciar sesion |
| POST | /auth/logout | Cerrar sesion |
| GET | /auth/me | Obtener usuario actual |
| GET | /customers | Listar todos los clientes |
| GET | /customers/summary | Resumen de clientes |
| GET | /customers/report | Reporte de clientes |
| GET | /customers/{id} | Obtener cliente por ID |
| POST | /customers | Crear cliente |
| PUT | /customers/{id} | Actualizar cliente |
| POST | /loans | Crear prestamo |
| GET | /loans | Listar prestamos |
| GET | /loans/report | Reporte de prestamos |
| GET | /loans/{id} | Obtener prestamo por ID |
| POST | /payments | Procesar pago |
| GET | /payments | Listar pagos |
| GET | /reports/projected-vs-actual | Proyectado vs Actual |
| GET | /reports/collection-availability | Disponibilidad de cobro |
| GET | /reports/client-profitability | Rentabilidad por cliente |

---

## 4. Modulo CustomerBC - Gestion de Clientes

### 4.1 Descripcion General

El modulo **CustomerBC** es responsable de la gestion completa de los clientes del sistema de prestamos. Maneja la creacion, actualizacion, consulta y eliminacion logica de clientes, incluyendo la encriptacion de datos sensibles como documentos de identidad, direcciones y numeros telefonicos.

### 4.2 Estructura de Archivos

```
app/CustomerBC/
├── Application/
│   ├── Commands/
│   │   ├── CreateCustomerCommand.php
│   │   └── UpdateCustomerCommand.php
│   ├── DTOs/
│   │   ├── CustomerResponse.php
│   │   ├── CustomerSummaryResponse.php
│   │   └── CustomerReportResponse.php
│   ├── Exceptions/
│   │   ├── CustomerAlreadyExistsException.php
│   │   └── CustomerNotFoundException.php
│   ├── UseCases/
│   │   ├── CreateCustomerUseCase.php
│   │   ├── UpdateCustomerUseCase.php
│   │   ├── GetCustomerByIdUseCase.php
│   │   ├── GetAllCustomersUseCase.php
│   │   ├── GetAllCustomersSummaryUseCase.php
│   │   └── GetCustomerReportUseCase.php
│   └── Config/
│       └── CustomerApplicationServiceProvider.php
├── Domain/
│   ├── Entities/
│   │   └── Customer.php
│   ├── ValueObjects/
│   │   └── CustomerIdVO.php
│   └── Repositories/
│       ├── CustomerFinderById.php
│       ├── CustomerFinderAll.php
│       ├── CustomerCreator.php
│       ├── CustomerUpdater.php
│       └── CustomerDniFinder.php
├── Infrastructure/
│   ├── Config/
│   │   └── CustomerInfrastructureServiceProvider.php
│   ├── Models/
│   │   └── CustomerModel.php
│   ├── Migrations/
│   │   └── 2024_01_01_000001_create_customers_table.php
│   ├── Persistence/
│   │   └── CustomerMapper.php
│   └── Repository/
│       ├── EloquentCustomerFinderById.php
│       ├── EloquentCustomerFinderAll.php
│       ├── EloquentCustomerCreator.php
│       ├── EloquentCustomerUpdater.php
│       └── EloquentCustomerDniFinder.php
└── Presentation/
    ├── Controllers/
    │   └── CustomerController.php
    └── Mappers/
        ├── CreateCustomerRequestMapper.php
        └── UpdateCustomerRequestMapper.php
```

### 4.3 Dominio (Domain Layer)

#### Entidad: Customer

**Archivo:** `app/CustomerBC/Domain/Entities/Customer.php`

**Descripcion:** Entidad principal que representa un cliente en el sistema.

**Propiedades:**
- `id`: CustomerIdVO - Identificador unico del cliente
- `personalData`: PersonVO - Datos personales (nombre, DNI, telefono, direccion, email)
- `createdAt`: DateVO - Fecha de creacion
- `enabled`: bool - Estado activo/inactivo del cliente

**Metodos principales:**
```php
// Crear nuevo cliente
public static function create(PersonVO $personalData, ?DateVO $createdAt = null): self

// Reconstruir entidad desde persistence
public static function reconstitute(CustomerIdVO $id, PersonVO $personalData, DateVO $createdAt, bool $enabled): self

// Metodos de comportamiento
public function disable(): self  // Deshabilitar cliente
public function enable(): self   // Habilitar cliente
public function isEnabled(): bool // Verificar estado
```

#### Value Object: CustomerIdVO

**Archivo:** `app/CustomerBC/Domain/ValueObjects/CustomerIdVO.php`

**Descripcion:** Identificador unico basado en UUID v7.

**Implementacion:** Extiende la interfaz `IdVO` del SharedKernel y utiliza la libreria `ramsey/uuid`.

```php
public static function fromString(string $value): static  // Crear desde string
public static function generate(): static                 // Generar nuevo UUID
public function getValue(): string                         // Obtener valor string
public function equals(self $other): bool                 // Comparar identificadores
```

#### Interfaces de Repositorio

**CustomerFinderById** - Busca un cliente por su ID:
```php
interface CustomerFinderById {
    public function findById(CustomerIdVO $id): ?Customer;
}
```

**CustomerFinderAll** - Obtiene todos los clientes:
```php
interface CustomerFinderAll {
    public function findAll(): array;
}
```

**CustomerCreator** - Crea nuevos clientes:
```php
interface CustomerCreator {
    public function create(Customer $customer): void;
}
```

**CustomerUpdater** - Actualiza clientes existentes:
```php
interface CustomerUpdater {
    public function update(Customer $customer): void;
}
```

**CustomerDniFinder** - Busca clientes por DNI (para evitar duplicados):
```php
interface CustomerDniFinder {
    public function existsByDni(string $dniNumber, DniType $dniType): bool;
}
```

### 4.4 Capa de Aplicacion (Application Layer)

#### Commands (Comandos)

**CreateCustomerCommand:**
```php
final class CreateCustomerCommand {
    public function __construct(
        public readonly PersonVO $personalData
    ) {}
}
```

**UpdateCustomerCommand:**
```php
final class UpdateCustomerCommand {
    public function __construct(
        public readonly string $id,
        public readonly PersonVO $personalData
    ) {}
}
```

#### Use Cases

**CreateCustomerUseCase:**
- Valida que el cliente no exista por DNI
- Crea el cliente con los datos proporcionados
- Persiste en la base de datos
- Retorna CustomerResponse

```php
final class CreateCustomerUseCase {
    public function __construct(
        private readonly CustomerCreator $creator,
        private readonly CustomerDniFinder $dniFinder
    ) {}

    public function execute(CreateCustomerCommand $command): CustomerResponse
}
```

**GetCustomerByIdUseCase:**
- Busca cliente por ID
- Lanza excepcion si no existe
- Retorna CustomerResponse

**GetAllCustomersUseCase:**
- Obtiene todos los clientes
- Retorna array de CustomerResponse

**UpdateCustomerUseCase:**
- Busca cliente existente
- Reconstruye con nuevos datos personales
- Actualiza en persistencia

**GetCustomerReportUseCase:**
- Genera reporte estadistico de clientes
- Usa CustomerLoanStatistics para datos de prestamos

#### DTOs (Data Transfer Objects)

**CustomerResponse:**
```php
final class CustomerResponse {
    public readonly string $id;
    public readonly string $name;
    public readonly string $dni;
    public readonly string $phone;
    public readonly string $address;
    public readonly ?string $email;
    public readonly string $createdAt;
    public readonly bool $enabled;

    public static function fromEntity($customer): self
    public function toArray(): array
}
```

### 4.5 Capa de Infraestructura (Infrastructure Layer)

#### Modelo Eloquent: CustomerModel

**Archivo:** `app/CustomerBC/Infrastructure/Models/CustomerModel.php`

```php
class CustomerModel extends Model {
    protected $table = 'customers';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'first_name', 'last_name', 'second_last_name', 'middle_name',
        'dni_number', 'dni_hash', 'dni_type', 'phone_number', 'phone_country_code',
        'address', 'email', 'enabled', 'created_at'
    ];

    protected $hidden = [
        'first_name', 'last_name', 'second_last_name', 'middle_name',
        'dni_number', 'phone_number', 'address'
    ];
}
```

#### Mapper: CustomerMapper

**Archivo:** `app/CustomerBC/Infrastructure/Persistence/CustomerMapper.php`

Convierte entre dominio y persistencia, manejando cifrado de datos sensibles:

```php
final class CustomerMapper {
    public function __construct(
        private readonly EncryptionService $encryption
    ) {}

    public function toDomain(CustomerModel $model): Customer
    public function toPersistence(Customer $customer): array
}
```

**Caracteristicas importantes:**
- Cifra nombres, DNI, telefono y direccion
- Genera hash del DNI para busquedas sin descifrar
- Descifra todos los datos al convertir a dominio

#### Repositorios Eloquent

- **EloquentCustomerFinderById** - Implementa `CustomerFinderById`
- **EloquentCustomerFinderAll** - Implementa `CustomerFinderAll`
- **EloquentCustomerCreator** - Implementa `CustomerCreator`
- **EloquentCustomerUpdater** - Implementa `CustomerUpdater`
- **EloquentCustomerDniFinder** - Implementa `CustomerDniFinder`

### 4.6 Capa de Presentacion (Presentation Layer)

#### Controlador: CustomerController

**Archivo:** `app/CustomerBC/Presentation/Controllers/CustomerController.php`

```php
final class CustomerController {
    public function store(Request $request): JsonResponse      // POST /customers
    public function show(string $id): JsonResponse             // GET /customers/{id}
    public function index(): JsonResponse                     // GET /customers
    public function summary(): JsonResponse                   // GET /customers/summary
    public function report(): JsonResponse                    // GET /customers/report
    public function update(Request $request, string $id): JsonResponse  // PUT /customers/{id}
}
```

#### Mappers de Request

**CreateCustomerRequestMapper:**
Convierte el request HTTP en CreateCustomerCommand, soportando multiples formatos de entrada.

```php
final class CreateCustomerRequestMapper {
    public function fromRequest(array $data): CreateCustomerCommand
}
```

**UpdateCustomerRequestMapper:**
Convierte el request HTTP en UpdateCustomerCommand.

### 4.7 Proveedores de Servicios

**CustomerApplicationServiceProvider:**
Registra todos los UseCases, Mappers y Controladores en el contenedor de Laravel.

**CustomerInfrastructureServiceProvider:**
Registra el servicio de cifrado, masking y todos los repositorios.

---

## 5. Modulo LoanBC - Gestion de Prestamos

### 5.1 Descripcion General

El modulo **LoanBC** gestiona todo lo relacionado con los prestamos: creacion, consulta, pagos, calculo de intereses y reportes de prestamos.

### 5.2 Estructura de Archivos

```
app/LoanBC/
├── Application/
│   ├── Commands/
│   │   ├── CreateLoanCommand.php
│   │   └── MakePaymentCommand.php
│   ├── DTOs/
│   │   ├── LoanResponse.php
│   │   └── LoanReportResponse.php
│   ├── UseCases/
│   │   ├── CreateLoanUseCase.php
│   │   ├── GetLoanByIdUseCase.php
│   │   ├── GetAllLoansUseCase.php
│   │   ├── MakePaymentUseCase.php
│   │   └── GetLoanReportUseCase.php
│   └── Config/
│       └── LoanServiceProvider.php
├── Domain/
│   ├── Entities/
│   │   └── Loan.php
│   ├── ValueObjects/
│   │   ├── LoanIdVO.php
│   │   ├── LoanStatus.php
│   │   └── InterestRateVO.php
│   ├── Services/
│   │   └── InterestCalculator.php
│   ├── Repositories/
│   │   ├── LoanFinderById.php
│   │   ├── LoanFinderAll.php
│   │   ├── LoanCreator.php
│   │   ├── LoanUpdater.php
│   │   └── LoanFinderByCustomerId.php
│   └── Ports/
│       └── CustomerLoanStatistics.php
├── Infrastructure/
│   ├── Models/
│   │   └── LoanModel.php
│   ├── Migrations/
│   │   └── 2024_01_01_000002_create_loans_table.php
│   ├── Persistence/
│   │   ├── LoanMapper.php
│   │   └── StubCustomerLoanStatistics.php
│   ├── Repository/
│   │   ├── EloquentLoanFinderById.php
│   │   ├── EloquentLoanFinderAll.php
│   │   ├── EloquentLoanCreator.php
│   │   ├── EloquentLoanUpdater.php
│   │   └── EloquentLoanFinderByCustomerId.php
│   └── Services/
│       └── InterestCalculatorService.php
└── Presentation/
    └── Controllers/
        └── LoanController.php
```

### 5.3 Dominio (Domain Layer)

#### Entidad: Loan

**Archivo:** `app/LoanBC/Domain/Entities/Loan.php`

**Descripcion:** Entidad que representa un prestamo, con logica de negocio para calculo de intereses y pagos.

**Propiedades:**
- `id`: LoanIdVO - Identificador unico del prestamo
- `customerId`: CustomerIdVO - ID del cliente asociado
- `originalCapital`: MoneyVO - Capital original del prestamo
- `capital`: MoneyVO - Capital actual (restante)
- `interestRate`: InterestRateVO - Tasa de interes
- `startDate`: DateVO - Fecha de inicio
- `dueDate`: DateVO - Fecha de vencimiento
- `createdAt`: DateVO - Fecha de creacion
- `status`: LoanStatus - Estado del prestamo
- `paidInterest`: MoneyVO - Intereses pagados
- `paidCapital`: MoneyVO - Capital pagado
- `remainingDebt`: MoneyVO - Deuda restante
- `nextPaymentDate`: DateVO - Proxima fecha de pago

**Metodos principales:**
```php
// Crear nuevo prestamo
public static function create(
    CustomerIdVO $customerId,
    MoneyVO $capital,
    InterestRateVO $interestRate,
    DateVO $startDate,
    DateVO $dueDate
): self

// Reconstruir desde BD
public static function reconstitute(...): self

// Logica de negocio
public function makePayment(MoneyVO $amount): self  // Procesar pago
public function isInDefault(): bool                 // Verificar mora
public function calculateCurrentInterest(): MoneyVO // Calcular interes actual
```

**Comportamiento - makePayment():**
El metodo `makePayment` implementa la logica de aplicacion de pagos:
1. Calcula el interes mensual actual
2. Si esta en mora, capitaliza el interes al capital
3. Primero aplica el pago al interes
4. Si hay remanente, lo aplica al capital
5. Actualiza la fecha del proximo pago
6. Cambia el estado a PAID si el capital es cero

#### Value Objects

**LoanIdVO:**
Identificador unico de prestamo basado en UUID v7.

**LoanStatus:**
```php
enum LoanStatus: string {
    case ACTIVE = 'active';
    case PAID = 'paid';
    case DEFAULTED = 'defaulted';
    case CANCELLED = 'cancelled';
}
```

**InterestRateVO:**
```php
final class InterestRateVO implements \Stringable {
    // Propiedades
    private readonly float $annualRate;   // Tasa anual
    private readonly float $monthlyRate;  // Tasa mensual

    // Metodos de factory
    public static function createAnnual(float $annualRate): self
    public static function createMonthly(float $monthlyRate): self

    // Metodos de negocio
    public function calculateInterest(MoneyVO $capital): MoneyVO
    public function calculateMonthlyInterestFromCapital(int $capitalAmount): int
}
```

### 5.4 Capa de Aplicacion (Application Layer)

#### Commands

**CreateLoanCommand:**
```php
final class CreateLoanCommand {
    public function __construct(
        public readonly CustomerIdVO $customerId,
        public readonly MoneyVO $capital,
        public readonly InterestRateVO $interestRate,
        public readonly DateVO $startDate,
        public readonly DateVO $dueDate
    ) {}
}
```

**MakePaymentCommand:**
```php
final class MakePaymentCommand {
    public function __construct(
        public readonly LoanIdVO $loanId,
        public readonly MoneyVO $amount
    ) {}
}
```

#### Use Cases

**CreateLoanUseCase:**
- Valida que el cliente existe
- Crea el prestamo con calculo de intereses inicial
- Persiste en base de datos
- Retorna LoanResponse

**GetLoanByIdUseCase:**
- Busca prestamo por ID
- Retorna LoanResponse

**GetAllLoansUseCase:**
- Lista todos los prestamos
- Includes datos del cliente (con decryption)

**MakePaymentUseCase:**
- Busca el prestamo
- Ejecuta makePayment en la entidad
- Actualiza el prestamo

**GetLoanReportUseCase:**
- Calcula estadisticas globales de prestamos
- Agrupa por status (active, paid, defaulted)
- Suma capital, deuda, intereses pagados

#### DTOs

**LoanResponse:**
```php
final class LoanResponse {
    public readonly string $id;
    public readonly string $customerId;
    public readonly array $capital;          // {amount, currency}
    public readonly array $remainingDebt;
    public readonly array $interestRate;     // {annual, monthly}
    public readonly string $startDate;
    public readonly string $dueDate;
    public readonly string $nextPaymentDate;
    public readonly string $status;
    public readonly array $paidCapital;
    public readonly array $paidInterest;
    public readonly string $createdAt;

    public function toArray(): array  // Incluye 'term' en meses
}
```

**LoanReportResponse:**
```php
final class LoanReportResponse {
    public readonly int $totalLoans;
    public readonly int $activeLoans;
    public readonly int $paidLoans;
    public readonly int $defaultedLoans;
    public readonly int $totalCapital;
    public readonly int $totalRemainingDebt;
    public readonly int $totalPaidCapital;
    public readonly int $totalPaidInterest;
}
```

### 5.5 Capa de Infraestructura (Infrastructure Layer)

#### Modelo Eloquent: LoanModel

```php
final class LoanModel extends Model {
    protected $table = 'loans';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'customer_id', 'original_capital', 'capital', 'remaining_debt',
        'paid_capital', 'paid_interest', 'interest_rate', 'start_date',
        'due_date', 'next_payment_date', 'status'
    ];

    protected $casts = [
        'original_capital' => 'integer',
        'capital' => 'integer',
        'interest_rate' => 'float',
        'next_payment_date' => 'datetime',
    ];
}
```

#### Mapper: LoanMapper

**Archivo:** `app/LoanBC/Infrastructure/Persistence/LoanMapper.php`

Convierte entre dominio y modelo Eloquent.

#### Repositorios Eloquent

- EloquentLoanFinderById
- EloquentLoanFinderAll
- EloquentLoanCreator
- EloquentLoanUpdater
- EloquentLoanFinderByCustomerId

#### Servicios: InterestCalculatorService

**Archivo:** `app/LoanBC/Infrastructure/Services/InterestCalculatorService.php`

Implementa la interfaz `InterestCalculator` para calculos de intereses:

```php
final class InterestCalculatorService implements InterestCalculator {
    public function calculateMonthlyInterest(MoneyVO $capital, float $annualRate): MoneyVO
    public function calculateInterestFromDate(MoneyVO $capital, float $annualRate, DateVO $fromDate): MoneyVO
    public function isInDefault(DateVO $dueDate): bool
    public function getDefaultDays(DateVO $dueDate): int
}
```

### 5.6 Capa de Presentacion

**LoanController:**

```php
final class LoanController {
    public function store(Request $request): JsonResponse     // POST /loans
    public function show(string $id): JsonResponse            // GET /loans/{id}
    public function index(): JsonResponse                      // GET /loans (incluye customer_name)
    public function report(): JsonResponse                    // GET /loans/report
    public function makePayment(Request $request, string $id): JsonResponse  // POST /loans/{id}/payment
}
```

**Nota:** El metodo `index()` incluye logica para obtener el nombre del cliente decryptando los datos.

---

## 6. Modulo PaymentBC - Gestion de Pagos

### 6.1 Descripcion General

El modulo **PaymentBC** gestiona el procesamiento de pagos, incluyendo la creacion, validacion y aplicacion de pagos a prestamos.

### 6.2 Estructura de Archivos

```
app/PaymentBC/
├── Application/
│   ├── Commands/
│   │   └── ProcessPaymentCommand.php
│   ├── DTOs/
│   │   └── PaymentResponse.php
│   └── UseCases/
│       └── ProcessPaymentUseCase.php
├── Domain/
│   ├── Entities/
│   │   └── Payment.php
│   ├── ValueObjects/
│   │   ├── PaymentIdVO.php
│   │   └── PaymentStatus.php
│   └── Repositories/
│       ├── PaymentFinderById.php
│       ├── PaymentFinderByLoanId.php
│       └── PaymentCreator.php
├── Infrastructure/
│   ├── Models/
│   │   └── PaymentModel.php
│   ├── Migrations/
│   │   └── 2024_01_01_000003_create_payments_table.php
│   └── Repositories/
│       ├── PaymentMapper.php
│       ├── EloquentPaymentFinderByLoanId.php
│       └── EloquentPaymentCreator.php
└── Presentation/
    ├── Controllers/
    │   └── PaymentController.php
    └── Providers/
        └── PaymentServiceProvider.php
```

### 6.3 Dominio (Domain Layer)

#### Entidad: Payment

**Archivo:** `app/PaymentBC/Domain/Entities/Payment.php`

**Descripcion:** Entidad que representa un pago con ciclo de vida completo.

**Estados (PaymentStatus):**
```php
enum PaymentStatus: string {
    case PENDING = 'pending';    // Pendiente de validacion
    case VALIDATED = 'validated'; // Validado
    case APPLIED = 'applied';    // Aplicado al prestamo
    case REJECTED = 'rejected';  // Rechazado
    case REFUNDED = 'refunded';  // Reembolsado
}
```

**Ciclo de vida del pago:**
1. Se crea en estado PENDING
2. Se valida -> estado VALIDATED
3. Se aplica al prestamo -> estado APPLIED
4. Opcionalmente se puede rechazar o reembolsar

**Metodos principales:**
```php
public static function create(LoanIdVO $loanId, MoneyVO $amount, ?DateVO $paymentDate = null): self

public function validate(): self       // Valida el pago
public function apply(MoneyVO $interestPortion, MoneyVO $capitalPortion): self  // Aplica al prestamo
public function reject(string $reason = ''): self  // Rechaza el pago
```

### 6.4 Capa de Aplicacion (Application Layer)

#### ProcessPaymentUseCase

```php
final class ProcessPaymentUseCase {
    public function __construct(
        private readonly PaymentCreator $paymentCreator,
        private readonly LoanFinderById $loanFinder,
        private readonly LoanUpdater $loanUpdater
    ) {}

    public function execute(ProcessPaymentCommand $command): PaymentResponse
}
```

**Flujo de procesamiento:**
1. Busca el prestamo por ID
2. Crea el pago con el monto
3. Valida el pago
4. Calcula la porcion de interes y capital
5. Aplica el pago al prestamo (si hay capital)
6. Actualiza el prestamo
7. Persiste el pago

### 6.5 Capa de Infraestructura

#### PaymentModel

```php
final class PaymentModel extends Model {
    protected $table = 'payments';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'loan_id', 'amount', 'payment_date',
        'interest_paid', 'capital_paid', 'status'
    ];
}
```

### 6.6 Capa de Presentacion

**PaymentController:**

```php
final class PaymentController {
    public function store(Request $request): JsonResponse  // POST /payments
    public function index(): JsonResponse                    // GET /payments (con datos de prestamo y cliente)
}
```

---

## 7. Modulo UserBC - Gestion de Usuarios

### 7.1 Descripcion General

El modulo **UserBC** gestiona la autenticacion y autorizacion de usuarios del sistema.

### 7.2 Estructura de Archivos

```
app/UserBC/
├── Application/
│   ├── Commands/
│   │   ├── CreateUserCommand.php
│   │   └── LoginCommand.php
│   ├── DTOs/
│   │   └── UserResponse.php
│   └── UseCases/
│       ├── CreateUserUseCase.php
│       └── LoginUseCase.php
├── Domain/
│   ├── Entities/
│   │   └── User.php
│   ├── ValueObjects/
│   │   └── UserIdVO.php
│   └── Repositories/
│       ├── UserFinderById.php
│       ├── UserFinderByEmail.php
│       └── UserCreator.php
├── Infrastructure/
│   ├── Models/
│   │   └── UserModel.php
│   └── Repositories/
│       ├── UserMapper.php
│       ├── EloquentUserFinderByEmail.php
│       └── EloquentUserCreator.php
└── Presentation/
    └── Controllers/
        └── AuthController.php
```

### 7.3 Dominio

#### Entidad: User

**Archivo:** `app/UserBC/Domain/Entities/User.php`

```php
final class User {
    private function __construct(
        private readonly UserIdVO $id,
        private readonly PersonVO $personalData,
        private readonly string $password,
        private readonly ?string $rememberToken,
        private readonly DateVO $emailVerifiedAt,
        private readonly DateVO $createdAt,
        private readonly bool $enabled
    ) {}

    public static function create(PersonVO $personalData, string $password): self
    public static function reconstitute(...): self
    public function verifyPassword(string $password): bool
    public function enable(): self
    public function disable(): self
}
```

### 7.4 Capa de Presentacion

**AuthController:**

```php
final class AuthController {
    public function login(Request $request): JsonResponse   // POST /auth/login
    public function logout(Request $request): JsonResponse  // POST /auth/logout
    public function me(Request $request): JsonResponse       // GET /auth/me
}
```

**Nota:** El controlador usa directamente el modelo User de Laravel (no el del modulo) para autenticacion simple con Laravel Sanctum.

---

## 8. Modulo ReportBC - Reportes y Analisis

### 8.1 Descripcion General

El modulo **ReportBC** proporciona reportes analiticos combinando datos de multiple modulos.

### 8.2 Estructura de Archivos

```
app/ReportBC/
├── Application/
│   ├── DTOs/
│   │   ├── ClientProfitabilityResponse.php
│   │   ├── CollectionAvailabilityResponse.php
│   │   └── ProjectedVsActualResponse.php
│   └── UseCases/
│       ├── GetClientProfitabilityUseCase.php
│       ├── GetCollectionAvailabilityUseCase.php
│       └── GetProjectedVsActualUseCase.php
└── Presentation/
    └── Controllers/
        └── ReportController.php
```

### 8.3 Use Cases

#### GetClientProfitabilityUseCase

Calcula la rentabilidad por cliente:
- Capital inicial
- Total intereses pagados
- Capital pagado
- Balance restante
- ROI (%) = (intereses / capital inicial) * 100

```php
final class GetClientProfitabilityUseCase {
    public function execute(): array  // Array de ClientProfitabilityResponse
}
```

#### GetCollectionAvailabilityUseCase

Analiza la disponibilidad de cobro:
- Prestamos activos
- Interes pendiente
- Estado: current, interest_default, interest_pending

#### GetProjectedVsActualUseCase

Compara proyecciones con valores reales:
- Balance anterior + interes mensual - pagos = Balance proyectado
- Diferencia entre balance proyectado y actual

### 8.4 Controlador

**ReportController:**

```php
final class ReportController {
    public function projectedVsActual(): JsonResponse     // GET /reports/projected-vs-actual
    public function collectionAvailability(): JsonResponse // GET /reports/collection-availability
    public function clientProfitability(): JsonResponse   // GET /reports/client-profitability
}
```

---

## 9. Modulo SharedKernel - Nucleo Compartido

### 9.1 Descripcion General

El **SharedKernel** contiene elementos compartidos entre todos los modulos, incluyendo Value Objectsbase, excepciones y servicios de infraestructura.

### 9.2 Value Objects Compartidos

#### Interfaces

**IdVO:**
```php
interface IdVO {
    public static function fromString(string $value): static;
    public static function generate(): static;
    public function getValue(): string;
}
```

#### Implementaciones

**UuidVO:**
Identificador universal unico usando ramsey/uuid.

```php
final class UuidVO implements \Stringable, IdVO {
    public static function fromString(string $value): static
    public static function generate(): static  // UUID v7
    public function getValue(): string
    public function equals(IdVO $other): bool
}
```

**MoneyVO:**
Representa dinero con soporte multi-moneda.

```php
final class MoneyVO implements \Stringable {
    private readonly int $amount;        // En centavos/unidades menores
    private readonly Currency $currency; // COP, USD, EUR

    public static function create(int $amount, Currency $currency = Currency::COP): self
    public static function zero(Currency $currency = Currency::COP): self

    public function getAmount(): int
    public function getCurrency(): Currency
    public function getFormatted(): string

    public function add(self $other): self
    public function subtract(self $other): self
    public function multiply(int $factor): self

    public function isZero(): bool
    public function isGreaterThan(self $other): bool
    public function isLessThan(self $other): bool
}
```

**Currency:**
```php
enum Currency: string {
    case COP = 'COP';
    case USD = 'USD';
    case EUR = 'EUR';

    public function symbol(): string
    public function decimalSeparator(): string
    public function thousandsSeparator(): string
}
```

**NameVO:**
Nombre completo con validacion.

```php
final class NameVO implements \Stringable {
    private readonly string $firstName;
    private readonly string $lastName;
    private readonly ?string $middleName;
    private readonly string $secondLastName;

    public static function create(
        string $firstName,
        string $lastName,
        string $secondLastName,
        ?string $middleName = null
    ): self

    public function getFirstName(): string
    public function getLastName(): string
    public function getFullName(): string  // Primer nombre + apellido + segundo apellido
    public function getShortName(): string // Primer nombre + apellido
}
```

**DniVO y DniType:**
Documento de identidad.

```php
enum DniType: string {
    case CC = 'CC';  // Cedula de ciudadania
    case CE = 'CE';  // Cedula de extranjeria
    case NIT = 'NIT';
    case PASSPORT = 'PASSPORT';
}

final class DniVO implements \Stringable {
    private readonly string $number;
    private readonly DniType $type;

    public static function create(string $number, DniType $type = DniType::CC): self
    public function getNumber(): string
    public function getType(): DniType
    public function getFormatted(): string
}
```

**PhoneVO:**
Telefono con codigo de pais.

```php
final class PhoneVO {
    private readonly string $countryCode;  // +57
    private readonly string $number;        // 3001234567

    public static function create(string $number, string $countryCode = '+57'): self
    public function getCountryCode(): string
    public function getNumber(): string
    public function getInternationalFormat(): string
    public function isColombian(): bool
}
```

**AddressVO:**
Direccion.

```php
final class AddressVO implements \Stringable {
    private readonly string $value;

    public static function create(string $address): self
    public function getValue(): string
}
```

**EmailVO:**
Correo electronico con validacion.

```php
final class EmailVO implements \Stringable {
    private readonly string $value;

    public static function create(string $email): self
    public function getValue(): string
    public function getLocalPart(): string
    public function getDomain(): string
}
```

**PersonVO:**
Agrega todos los datos personales.

```php
final class PersonVO implements \Stringable {
    private readonly NameVO $name;
    private readonly DniVO $dni;
    private readonly PhoneVO $phone;
    private readonly AddressVO $address;
    private readonly ?EmailVO $email;

    public static function create(
        NameVO $name,
        DniVO $dni,
        PhoneVO $phone,
        AddressVO $address,
        ?EmailVO $email = null
    ): self

    // Metodos with* para inmutabilidad
    public function withName(NameVO $name): self
    public function withEmail(?EmailVO $email): self
    // ...
}
```

**DateVO:**
Fecha inmutable.

```php
final class DateVO implements \Stringable {
    private readonly DateTimeInterface $date;

    public static function create(DateTimeInterface|string $date): self
    public static function today(): self
    public static function now(): self

    public function getYear(): int
    public function getMonth(): int
    public function getDay(): int
    public function getFormatted(string $format = 'Y-m-d'): string

    public function isAfter(self $other): bool
    public function isBefore(self $other): bool
    public function diffInDays(self $other): int
    public function addMonths(int $months): self

    public function isFuture(): bool
    public function isPast(): bool
}
```

### 9.3 Excepciones

#### Excepciones de Dominio

**DomainException** (abstracta) - Clase base para excepciones de dominio.

Excepciones de validacion de Value Objects:
- InvalidEmailException
- InvalidUuidException
- InvalidDniException
- InvalidNameException
- InvalidPhoneException
- InvalidAddressException
- InvalidMoneyException
- EntityNotFoundException
- AggregateNotFoundException
- BusinessRuleViolationException

#### Excepciones de Aplicacion

**ApplicationException** (abstracta) - Clase base para excepciones de aplicacion.

- ValidationException
- NotFoundException
- ConflictException
- UnauthorizedException
- ForbiddenException
- ServiceUnavailableException

#### Excepciones de Infraestructura

**InfrastructureException** (abstracta) - Clase base para excepciones de infraestructura.

- DatabaseException
- RepositoryException
- ExternalServiceException
- CacheException

### 9.4 Servicios de Infraestructura

#### EncryptionService (Puerto)

```php
interface EncryptionService {
    public function encrypt(string $plaintext): string
    public function decrypt(string $ciphertext): string
    public function hash(string $value): string
    public function verify(string $value, string $hash): bool
}
```

**LaravelEncryptionService** (Implementacion):
```php
final class LaravelEncryptionService implements EncryptionService {
    // Usa Crypt::encryptString() y Hash::make() de Laravel
}
```

#### MaskingService (Puerto)

```php
interface MaskingService {
    public function maskEmail(string $email): string
    public function maskPhone(string $phone): string
    public function maskDni(string $dni, string $type): string
    public function mask(string $value, int $visibleAtStart = 4, string $maskChar = '*'): string
    public function maskEnd(string $value, int $visibleAtEnd = 4, string $maskChar = '*'): string
}
```

### 9.5 Manejo de Excepciones

**ExceptionMapper:**

**Archivo:** `app/SharedKernel/Presentation/Exceptions/ExceptionMapper.php`

Mapea excepciones a respuestas HTTP con formato estandar:

```php
final class ExceptionMapper {
    public function map(Throwable $e, Request $request): JsonResponse

    // Mapea segun tipo de excepcion:
    // - DomainException -> 422
    // - ApplicationException -> codigo apropiado
    // - InfrastructureException -> 503
}
```

**Formato de respuesta:**
```json
{
    "error": {
        "type": "DOMAIN_ERROR|APPLICATION_ERROR|INFRASTRUCTURE_ERROR|INTERNAL_ERROR",
        "code": "CodigoExcepcion",
        "message": "Mensaje legible"
    },
    "trace_id": "optional-trace-id"
}
```

---

## 10. Esquema de Base de Datos

### 10.1 Tabla: customers

**Migracion:** `2024_01_01_000001_create_customers_table.php`

```sql
CREATE TABLE customers (
    id UUID PRIMARY KEY,
    first_name VARCHAR(255),           -- CIFRADO
    last_name VARCHAR(255),           -- CIFRADO
    second_last_name VARCHAR(255),    -- CIFRADO
    middle_name VARCHAR(255),        -- CIFRADO
    dni_number VARCHAR(255),          -- CIFRADO
    dni_hash VARCHAR(255),            -- HASH para busqueda
    dni_type VARCHAR(20),
    phone_number VARCHAR(255),        -- CIFRADO
    phone_country_code VARCHAR(10),
    address TEXT,                     -- CIFRADO
    email VARCHAR(255),
    enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Indices
INDEX idx_dni_type (dni_type);
INDEX idx_dni_hash (dni_hash);
INDEX idx_email (email);
INDEX idx_enabled (enabled);
```

### 10.2 Tabla: loans

**Migracion:** `2024_01_01_000002_create_loans_table.php`

```sql
CREATE TABLE loans (
    id UUID PRIMARY KEY,
    customer_id UUID REFERENCES customers(id),
    original_capital BIGINT UNSIGNED,
    capital BIGINT UNSIGNED,
    remaining_debt BIGINT UNSIGNED,
    paid_capital BIGINT UNSIGNED DEFAULT 0,
    paid_interest BIGINT UNSIGNED DEFAULT 0,
    interest_rate DECIMAL(5,2),
    start_date DATE,
    due_date DATE,
    next_payment_date DATE,
    status ENUM('active', 'paid', 'defaulted', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Indices
INDEX idx_customer_id (customer_id);
INDEX idx_status (status);
INDEX idx_next_payment_date (next_payment_date);
```

### 10.3 Tabla: payments

**Migracion:** `2024_01_01_000003_create_payments_table.php`

```sql
CREATE TABLE payments (
    id UUID PRIMARY KEY,
    loan_id UUID REFERENCES loans(id),
    amount BIGINT UNSIGNED,
    payment_date DATETIME,
    interest_paid BIGINT UNSIGNED DEFAULT 0,
    capital_paid BIGINT UNSIGNED DEFAULT 0,
    status ENUM('pending', 'validated', 'applied', 'rejected', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Indices
INDEX idx_loan_id (loan_id);
INDEX idx_status (status);
```

### 10.4 Tabla: users (Laravel default)

El sistema usa la tabla `users` por defecto de Laravel para autenticacion.

---

## 11. Patrones de Diseno Utilizados

### 11.1 Domain-Driven Design (DDD)

- **Bounded Contexts**: Cada modulo (CustomerBC, LoanBC, etc.) representa un contexto delimitado
- **Entities**: Customer, Loan, Payment, User
- **Value Objects**: MoneyVO, DateVO, NameVO, DniVO, etc.
- **Aggregates**: Loan como aggregate raiz
- **Domain Services**: InterestCalculator

### 11.2 Repository Pattern

- Interfaces en la capa de Dominio
- Implementaciones en la capa de Infraestructura
- Separacion clara entre logica de negocio y acceso a datos

### 11.3 Command Query Responsibility Segregation (CQRS)

- **Commands**: CreateCustomerCommand, CreateLoanCommand, MakePaymentCommand, etc.
- **Queries**: UseCases de lectura (GetCustomerByIdUseCase, GetAllLoansUseCase, etc.)

### 11.4 Factory Methods

- `create()` para creacion normal
- `reconstitute()` para reconstruccion desde persistencia

### 11.5 Value Objects Inmutables

Todos los Value Objects son inmutables:
- Constructor privado
- Metodos que retornan nuevas instancias
- Implementacion de `\Stringable` para conversion a string

### 11.6 Dependency Injection

- Uso extensivo de constructor injection
- Service Providers de Laravel para registro de dependencias

### 11.7 Data Transfer Objects (DTOs)

- Respuestas estandarizadas en formato JSON
- Metodo `toArray()` para serializacion
- Metodo `fromEntity()` para conversion desde entidades

### 11.8 Mapper Pattern

- **CustomerMapper**: Convierte entre Customer y CustomerModel
- **LoanMapper**: Convierte entre Loan y LoanModel
- **PaymentMapper**: Convierte entre Payment y PaymentModel
- **UserMapper**: Convierte entre User y UserModel
- Manejo de cifrado/descifrado en los mappers

### 11.9 cifrado de Datos Sensibles

El sistema implementa cifrado de datos personales en la base de datos:
- Nombres completos
- Numero de documento (DNI)
- Numeros de telefono
- Direcciones

Esto proporciona una capa adicional de seguridad mas alla de las medidas tradicionales de acceso.

---

## Apendice: Configuracion de Servicios

### Proveedores de Servicios

Cada modulo tiene su propio Service Provider que registra:
1. Repositorios (bindings)
2. UseCases
3. Mappers
4. Controladores

Los proveedores se cargan en `config/app.php`.

---

*Documento generado automaticamente*
*Fecha: Abril 2026*
*Proyecto: Loan Manager - Backend Laravel*
