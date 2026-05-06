export interface ReportSummaryDTO {
    portfolio: {
        total_prestado: number
        capital_pendiente: number
        intereses_generados: number
        intereses_cobrados: number
        numero_prestamos_activos: number
        tasa_interes_promedio: number
        total_clientes: number
    }
    kpis: {
        tasa_mora: number
        tasa_recuperacion: number
        ticket_promedio: number
        duracion_promedio_prestamo: number
        porcentaje_clientes_recurrentes: number
        total_prestamos: number
        total_clientes: number
        total_prestamos_cerrados: number
    }
    collection: {
        anio: string
        mes: string
        monto_cobrado: number
        monto_esperado: number
        numero_cuotas_vencidas: number
        numero_pagos: number
        porcentaje_cumplimiento: number
    }
    delinquency: {
        clientes_en_mora: number
        monto_en_mora: number
        porcentaje_cartera_vencida: number
    }
}

export interface ReportPortfolioDTO {
    capital_pendiente: number
    intereses_cobrados: number
    intereses_generados: number
    numero_prestamos_activos: number
    tasa_interes_promedio: number
    total_clientes: number
    total_prestado: number
}

export interface ReportProfitableDTO {
    ratio_intereses_capital: number
    roi_global: number
    roi_por_prestamo: {
        loan_id:string
        loan_number:string
        customer_name: string
        capital: number
        dias_activo: number
        intereses_cobrados:number
        roi: number
    }[]
    total_capital: number
    total_intereses: number
    total_prestamos: number
}

export interface ReportDeliquencyDTO {
    clientes_en_mora: number
    detalle_mora: {
        loan_id: string
        loan_number: string
        customer_name: string
        saldo_pendiente: number
        dias_atrazo: number
    }[]
    dias_promedio_atraso: number
    monto_en_mora: number
    porcentaje_cartera_vencida: number
    prestamos_en_mora: number
}

export interface CustomerKPIDTO{
    total_customers: number,
    customers_with_active_loans: number,
    customers_with_loans: number,
    customers_without_loans: number,
    active_customers: number
}

export interface CashFlowParamsDTO {
    fecha_inicio: string
    fecha_fin: string
}

export interface CashFLowResponseDTO {
    ingresos_por_pagos: number
    egresos_por_desembolsos: number
    flujo_neto: number
    total_desembolsos: number
    total_pagos: number
    fecha_fin: string
    fecha_inicio: string
}
