export interface ReportSummary {
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
  collection: any
  delinquency: {
    clientes_en_mora: number
    monto_en_mora: number
    porcentaje_cartera_vencida: number
  }
}

export interface CustomerKPI {
  totalCustomers: number,
  customersWithActiveLoans: number,
  customersWithLoans: number,
  customersWithoutLoans: number,
  activeCustomers: number
}

export interface CashFlowParams {
  fecha_inicio: string
  fecha_fin: string
}