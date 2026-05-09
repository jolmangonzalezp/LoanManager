# ¿Cómo funciona LoanManager? (Lógica de Negocio)

Este documento explica la "inteligencia" detrás del sistema. No es una guía de botones, sino una explicación de cómo el sistema piensa y procesa la información de tu negocio utilizando el lenguaje propio del mundo financiero.

---

## 1. El Concepto de "Cartera Inteligente"
El sistema no es solo una base de datos; es un guardián de las reglas de tu negocio. Está diseñado para que las reglas de préstamos sean siempre las mismas, sin importar si los datos se guardan en la nube o en un servidor local. Esto garantiza que tus cuentas nunca fallen.

---

## 2. El Ciclo de Vida del Dinero
El sistema entiende que un préstamo no es solo un papel, sino un proceso vivo que pasa por varias etapas:

1.  **Originación:** Cuando entregas el dinero (Capital), el sistema calcula inmediatamente cuánto interés se va a generar y cuándo debería regresar ese dinero a tus manos.
2.  **Vigencia (Activo):** Mientras el cliente te deba dinero, el sistema vigila el calendario. Si pasa la fecha de pago, el sistema marca el préstamo como "en mora" automáticamente.
3.  **Liquidación:** El préstamo solo se considera "finalizado" cuando la deuda llega exactamente a cero.

---

## 3. La Regla de Oro: Distribución de Pagos
Esta es la parte más importante del sistema. Cuando un cliente hace un **abono**, el sistema sigue una lógica financiera estricta:

- **Prioridad al Interés:** El sistema primero cobra los intereses que se han generado hasta ese momento. Esto asegura que tu rentabilidad esté protegida.
- **Amortización del Capital:** Solo después de cubrir los intereses, el dinero sobrante se resta de la deuda principal (Capital). 
- **Recálculo Inmediato:** En cuanto se registra el pago, el sistema vuelve a calcular cuánto se debe, para que el interés del próximo mes sea sobre el nuevo saldo real.

---

## 4. Precisión Centavo a Centavo
A diferencia de una hoja de cálculo común, el sistema trata el dinero de forma especial:
- **Sin errores de redondeo:** El sistema no usa números con decimales comunes (que a veces pierden centavos en el camino). Maneja los montos como unidades exactas de dinero para que el cierre de caja siempre cuadre al 100%.
- **Fechas Inalterables:** Las fechas de los pagos y vencimientos son fijas y el sistema las usa para medir la puntualidad de los clientes sin margen de error.

---

## 5. Áreas de Responsabilidad (Contextos)
El sistema está dividido en "departamentos" que trabajan juntos pero no se mezclan:
- **Área de Clientes:** Se encarga de saber quién es quién y cuál es su historial de confianza.
- **Área de Préstamos:** Es la calculadora financiera que maneja los contratos y las tasas.
- **Área de Recaudación:** Registra cada peso que entra y lo asigna al préstamo correcto.
- **Área de Seguridad:** Controla quién tiene permiso para ver las ganancias y quién solo puede registrar datos básicos.

---

## 6. La Verdad Única
El sistema está construido para que haya una "fuente de verdad". Si el área de préstamos dice que un cliente debe 100 pesos, el área de reportes mostrará exactamente lo mismo. No hay duplicidad de datos ni información contradictoria, lo que te da la tranquilidad de que tus reportes financieros son reales y confiables.

---
*Este documento describe la filosofía y la lógica interna con la que LoanManager protege y administra tu capital.*
