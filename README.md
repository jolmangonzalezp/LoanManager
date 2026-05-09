# Guía Técnica Completa: Proyecto LoanManager

## 1. Introducción y Propósito
LoanManager es una plataforma empresarial para la gestión del ciclo de vida de préstamos financieros. El sistema ha sido diseñado para ser escalable, mantenible y altamente testeable, utilizando una arquitectura desacoplada basada en contextos delimitados (**Bounded Contexts**) y siguiendo los principios de **Domain-Driven Design (DDD)**.

## 2. Arquitectura del Sistema

### 2.1. Estructura de Capas (Arquitectura Hexagonal)
Cada contexto en el sistema sigue una estructura de cuatro capas para asegurar la separación de responsabilidades:

1.  **Capa de Dominio (`Domain`):**
    - El "corazón" del sistema. Contiene los **Agregados**, **Entidades** y **Value Objects**.
    - Define las interfaces de los repositorios y servicios de dominio.
    - No tiene dependencias externas (es código PHP puro).
    - Ejemplo: `Loan.php`, `MoneyVO.php`.

2.  **Capa de Aplicación (`Application`):**
    - Orquestación de la lógica de negocio.
    - Contiene los **Casos de Uso** (ej: `CreateLoanUseCase`).
    - Define **DTOs** (Data Transfer Objects) para la comunicación entre capas.
    - Implementa comandos y consultas (CQRS).

3.  **Capa de Infraestructura (`Infrastructure`):**
    - Implementaciones técnicas específicas: Persistencia con Eloquent, envío de correos, logging.
    - Contiene los **Mappers** que transforman modelos de base de datos a entidades de dominio.
    - Implementa las interfaces definidas en la capa de dominio.

4.  **Capa de Presentación/Presenter (`Presentation`/`Presenter`):**
    - Punto de entrada al sistema (Controladores de API).
    - Gestión de validaciones de entrada (`FormRequests`).
    - Transformación de respuestas para el cliente.

---

## 3. Bounded Contexts (Contextos Delimitados)

### 3.1. CustomerBC (Gestión de Clientes)
Responsable de toda la información de los prestatarios.
- **Entidad Principal:** `Customer`.
- **Value Objects:** `PersonVO` (agrupa nombre, apellidos y DNI), `AddressVO`, `EmailVO`.
- **Flujos:** Registro de nuevos clientes, actualización de datos de contacto y validación de identidad.

### 3.2. LoanBC (Núcleo de Préstamos)
Es el contexto más crítico. Maneja las reglas financieras.
- **Agregado:** `Loan`.
- **Reglas de Interés:** Implementa cálculos de interés basados en saldos insolutos.
- **Distribución de Abonos:** Lógica para separar un pago en porción de capital e interés.
- **Estados:** `ACTIVE` (Activo), `PAID` (Pagado), `INACTIVE` (Desactivado).

### 3.3. PaymentBC (Procesamiento de Pagos)
Gestiona las transacciones financieras entrantes.
- **Agregado:** `Payment`.
- **Integración:** Interactúa con `LoanBC` para actualizar el estado de las deudas tras un pago exitoso.
- **Reportes:** Genera historiales de pago por cliente y por periodo mensual.

### 3.4. UserBC (Seguridad y RBAC)
Gestión de accesos basada en roles y permisos.
- **Roles:** Definición de perfiles (Administrador, Operador, Auditor).
- **Permisos:** Control granular sobre acciones específicas (crear préstamo, ver reportes).
- **Autenticación:** Utiliza tokens de API vía Laravel Sanctum.

### 3.5. SharedKernel (Núcleo Compartido)
Contiene elementos reutilizables por múltiples contextos para evitar la duplicación de código.
- **Value Objects Comunes:** `MoneyVO` (para manejo de divisas y precisión decimal), `DateVO` (manejo uniforme de fechas), `UuidVO`.
- **Excepciones de Dominio:** Base para errores de negocio.

---

## 4. Patrones de Diseño Implementados

### 4.1. Repositorios Granulares
En lugar de un único repositorio por entidad, el sistema utiliza clases especializadas por acción (Interface Segregation):
- `LoanCreator`: Solo para guardar nuevos préstamos.
- `LoanFinder`: Para búsquedas por ID o criterios específicos.
- `LoanUpdater`: Para modificar estados y balances.
Esto facilita el cumplimiento de **SOLID** y simplifica las pruebas unitarias.

### 4.2. Mappers
Los mappers en la capa de `Infrastructure` aseguran que el dominio nunca conozca los detalles de la base de datos (Eloquent).
- `toDomain()`: Convierte un modelo de Eloquent a una Entidad de Dominio rica.
- `toPersistence()`: Convierte una Entidad a un array plano para la base de datos.

### 4.3. Value Objects (VO)
Toda la información crítica se encapsula en VOs que son inmutables.
- Un `MoneyVO` asegura que no se realicen operaciones matemáticas erróneas con montos.
- Un `DateVO` garantiza un formato consistente en todo el sistema.

---

## 5. Frontend: Arquitectura Modular (Vue + TypeScript)

El frontend está organizado en módulos que reflejan los Bounded Contexts del backend en `resources/js/Modules/`:

- **Componentes:** UI específica del módulo (ej: `LoanFormComponent.vue`).
- **Composables:** Lógica reactiva reutilizable (`useLoan.ts`).
- **Services/Api:** Capa de comunicación con el servidor mediante Axios.
- **Mappers:** Transformación de datos crudos de la API a interfaces de TypeScript.
- **Types:** Definiciones estrictas para asegurar la integridad de los datos en tiempo de desarrollo.

---

## 6. Infraestructura y Despliegue

### 6.1. Entorno de Producción (Laravel Cloud)
Aunque el proyecto cuenta con soporte para contenedores, la aplicación está oficialmente desplegada en **Laravel Cloud**. Esto implica:
- **Infraestructura Serverless:** Escalado automático gestionado por la plataforma.
- **Gestión de Secretos:** Las variables de entorno críticas se gestionan directamente en el panel de Laravel Cloud.
- **Optimización de Activos:** Uso nativo de Vite para la compilación y entrega de assets frontend.
- **Bases de Datos:** Integración con servicios de bases de datos gestionados compatibles con Eloquent.

### 6.2. Entorno Local (Docker)
Para el desarrollo local, se utiliza una configuración estandarizada que garantiza la paridad de entornos:
- **Docker:** Configuración multi-etapa para optimizar imágenes y facilitar el onboarding de nuevos desarrolladores.
- **Nixpacks:** Soporte para compilaciones automatizadas y despliegues modernos.
- **Sail/Pail:** Integración con herramientas nativas de Laravel para debugging y logs en contenedores.

## 7. Calidad y Estándares de Desarrollo

### 7.1. Calidad de Código
- **PHP Linting:** Laravel Pint (configurado en `pint.json`).
- **JS/TS Linting:** ESLint + Prettier.
- **Type Checking:** `vue-tsc` para asegurar que las plantillas de Vue respeten los tipos.

### 7.2. Pruebas (Testing)
- **Framework:** Pest PHP.
- **Enfoque:**
    - **Unitarias:** Lógica de Dominio y Value Objects.
    - **Integración:** Casos de Uso y Repositorios.
    - **API:** Contratos de controladores y respuestas JSON.

## 8. Procedimientos Operativos

### 8.1. Crear un Nuevo Caso de Uso
1. Definir el **Command** o **Query** en `Application`.
2. Crear la interfaz necesaria en `Domain/Repository`.
3. Implementar la lógica en la clase `UseCase`.
4. Implementar la persistencia en `Infrastructure`.
5. Exponer a través de un controlador en `Presenter`.

### 8.2. Configuración de Entorno
El archivo `.env` controla parámetros clave como:
- `DB_CONNECTION`: Configuración de base de datos.
- `SANCTUM_STATEFUL_DOMAINS`: Para autenticación SPA.
- `APP_KEY`: Clave de cifrado del framework.

---
*Documento de Referencia Técnica - Versión 2.1*
