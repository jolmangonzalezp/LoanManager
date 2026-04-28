import { ref } from 'vue'
import { ReportService } from '../Service/Report.Service'

const porfolioReport = ref<{
  capital_pendiente: number
  intereses_cobrados: number
  intereses_generados: number
  numero_prestamos_activos: number
  tasa_interes_promedio: number
  total_clientes: number
  total_prestado: number
} | null>(null)

const profitabilityReport = ref<{
  ratio_intereses_capital: number
  roi_global: number
  roi_por_prestamo: {
    capital: number
    customer_name: string
    dias_activo: number
    intereses_cobrados: number
    loan_id: string
    loan_number: string
    roi: number
  }[]
  total_capital: number
  total_intereses: number
  total_prestamos: number
} | null>(null)

export function useReports() {
  const getPortfolio = async (): Promise<void> => {
    porfolioReport.value = await ReportService.getPortfolio()
  }

  const getProfitability = async (): Promise<void> => {
    profitabilityReport.value = await ReportService.getProfitability()
  }

  return {
    porfolioReport,
    profitabilityReport,
    getPortfolio,
    getProfitability
  }
}