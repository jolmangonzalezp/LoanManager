export interface ReportSummary {
  portfolio: {
    totalPrestado: number
    capitalPendiente: number
    interesesGenerados: number
    interesesCobrados: number
    numeroPrestamosActivos: number
    tasaInteresPromedio: number
    totalClientes: number
  }
  kpis: {
    tasaMora: number
    tasaRecuperacion: number
    ticketPromedio: number
    duracionPromedioPrestamo: number
    porcentajeClientesRecurrentes: number
    totalPrestamos: number
    totalClientes: number
    totalPrestamosCerrados: number
  }
    collection: {
        anio: string
        mes: string
        montoCobrado: number
        montoEsperado: number
        numeroCuotasVencidas: number
        numeroPagos: number
        porcentajeCumplimiento: number
    }
  delinquency: {
    clientesEnMora: number
    montoEnMora: number
    porcentajeCarteraVencida: number
  }
}

export interface ReportPortfolio {
    capitalPendiente: number
    interesesCobrados: number
    interesesGenerados: number
    numeroPrestamosActivos: number
    tasaInteresPromedio: number
    totalClientes: number
    totalPrestado: number
}

export interface ReportProfitable {
    ratioInteresesCapital: number
    roiGlobal: number
    roiPorPrestamo: {
        capital: number
        customerName: string
        diasActivo: number
        interesesCobrados: number
        loanId: string
        loanNumber: string
        roi: number
    }[]
    totalCapital: number
    totalIntereses: number
    totalPrestamos: number
}

export interface ReportDeliquency {
    clientesEnMora: number
    detalleMora: {
        loanId: string
        loanNumber: string
        customerName: string
        saldoPendiente: number
        diasAtrazo: number
    }[]
    diasPromedioAtraso: number
    montoEnMora: number
    porcentajeCarteraVencida: number
    prestamosEnMora: number
}

export interface CustomerKPI {
  totalCustomers: number,
  customersWithActiveLoans: number,
  customersWithLoans: number,
  customersWithoutLoans: number,
  activeCustomers: number
}

export interface CashFlowParams {
  fechaInicio: string
  fechaFin: string
}

export interface CashFLowResponse {
    ingresos: number
    egresos: number
    flujoNeto: number
    totalDesembolsos: number
    totalPagos: number
    fechaFin: string
    fechaInicio: string
}
