# Manual Técnico de Arquitectura y Desarrollo: Proyecto LoanManager

## 1. Introducción
**LoanManager** es una solución empresarial de grado financiero para la gestión automatizada del ciclo de vida de préstamos. Este manual proporciona una visión exhaustiva de la arquitectura, los patrones de diseño y los estándares operativos necesarios para el mantenimiento y escalabilidad del sistema.

---

## 2. Arquitectura del Sistema: Domain-Driven Design (DDD)
El sistema implementa una **Arquitectura Hexagonal** (Puertos y Adaptadores) organizada en **Bounded Contexts (BC)**. Esto garantiza un desacoplamiento total entre la lógica de negocio y las implementaciones técnicas.

### 2.1. Capas Arquitectónicas
1.  **Dominio (Domain):** Contiene la lógica central, Agregados (Entidades raíz), Value Objects e Interfaces de Repositorio. Es agnóstico a cualquier framework.
2.  **Aplicación (Application):** Implementa los **Casos de Uso** orquestando entidades de dominio y puertos de infraestructura. Utiliza el patrón **CQRS** para separar lecturas y escrituras.
3.  **Infraestructura (Infrastructure):** Adaptadores para persistencia (Eloquent), servicios externos, y mappers que transforman datos entre capas.
4.  **Presentación (Presentation/Presenter):** Controladores API REST, Validación de Requests y Transformación de respuestas para el cliente.

### 2.2. Bounded Contexts y Responsabilidades
| Contexto | Responsabilidad Principal | Entidad Raíz |
| :--- | :--- | :--- |
| **CustomerBC** | Gestión de identidad y datos del prestatario. | `Customer` |
| **LoanBC** | Motor financiero: originación, cálculo de intereses y estados. | `Loan` |
| **PaymentBC** | Procesamiento de abonos y conciliación financiera. | `Payment` |
| **UserBC** | Seguridad, RBAC (Roles/Permisos) y Autenticación. | `User` |
| **SharedKernel** | Objetos de valor y utilidades transversales inmutables. | N/A |

---

## 3. Especificación de Value Objects (SharedKernel)
El uso de **Value Objects** garantiza la integridad de los datos en todo el sistema:
- **`MoneyVO`:** Gestiona montos en unidades enteras para evitar errores de coma flotante. Implementa operaciones aritméticas seguras (`add`, `subtract`).
- **`DateVO`:** Encapsula `DateTimeImmutable` y proporciona métodos de comparación (`isAfter`, `isBefore`) y manipulación (`addMonths`) consistentes.
- **`UuidVO`:** Identificadores únicos universales para todas las entidades de dominio.

---

## 4. Patrones de Diseño Avanzados

### 4.1. Repositorios Segregados (ISP)
El sistema no utiliza un repositorio monolítico. En su lugar, utiliza interfaces segregadas:
- **`Creator`:** Operaciones de persistencia inicial.
- **`Finder`:** Consultas de recuperación de datos.
- **`Updater`:** Gestión de cambios de estado y balances.

### 4.2. Mappers de Infraestructura
Los mappers (ej: `LoanMapper`) actúan como traductores bidireccionales:
- **`toDomain`:** Reconstituye un Agregado de Dominio a partir de un modelo de base de datos.
- **`toPersistence`:** Descompone un Agregado en estructuras planas compatibles con Eloquent.

---

## 5. Referencia de la API REST

### 5.1. Autenticación (Sanctum)
- `POST /api/auth/login`: Genera token de acceso.
- `GET /api/auth/me`: Retorna el perfil del usuario autenticado.

### 5.2. Operaciones de Negocio
| Endpoint | Método | Contexto | Acción |
| :--- | :--- | :--- | :--- |
| `/api/customers` | `GET/POST` | Customer | Listar o registrar clientes. |
| `/api/loans` | `GET/POST` | Loan | Consultar u originar préstamos. |
| `/api/loans/{id}/payment` | `POST` | Loan/Payment | Registrar abono y recalcular saldos. |
| `/api/payments/monthly` | `GET` | Payment | Reporte consolidado de recaudación. |
| `/api/reports/profitability`| `GET` | Report | Métricas de rentabilidad por cliente. |

---

## 6. Manejo Global de Errores
El sistema implementa una tubería de excepciones centralizada en `bootstrap/app.php`:
1.  **Middleware `HandleExceptions`:** Intercepta excepciones de infraestructura y dominio.
2.  **`ErrorMapper`:** Transforma excepciones técnicas en respuestas JSON estandarizadas con códigos de error de negocio legibles.

---

## 7. Frontend: Arquitectura Vue + TypeScript
Ubicado en `resources/js/`, el frontend es un reflejo de la arquitectura del backend:
- **Modularidad:** Cada carpeta en `Modules/` es independiente y contiene sus propios componentes, tipos y servicios.
- **Composables (`useLoan`, `useCustomer`):** Encapsulan el estado reactivo y la lógica de negocio del lado del cliente.
- **Seguridad:** Los guardias de ruta de Vue verifican roles y permisos antes de permitir el acceso a vistas críticas.

---

## 8. Despliegue e Infraestructura

### 8.1. Producción: Laravel Cloud
- **Modelo:** Serverless (AWS Lambda gestionado).
- **Escalado:** Automático bajo demanda.
- **Assets:** Compilados con Vite y servidos vía CDN.

### 8.2. Desarrollo: Docker & Laravel Sail
- Utiliza contenedores aislados para PHP 8.3, MySQL, Redis y Mailpit.
- Comando de inicio: `composer dev`.

---

## 9. Calidad y Mantenimiento

### 9.1. Estándares de Código
- **PHP:** PSR-12 vía Laravel Pint.
- **JS/TS:** ESLint y Prettier.
- **Documentación:** El código debe incluir Type Hinting estricto y docblocks en el dominio.

### 9.2. Estrategia de Testing (Pest PHP)
- **Unit:** 100% de cobertura en lógica financiera de Agregados.
- **Feature:** Validación de flujos completos (Creación de préstamo -> Pago -> Reporte).

---
*Documentación técnica exhaustiva - Última actualización: 9 de Mayo de 2026*
