export { ReportApi } from './Api/Report.Api'
export { ReportService } from './Service/Report.Service'
export { useReports } from './Composable/useReports'
export { useReports as useReportsView } from './Composable/useReports'

// Alias for backward compatibility
export const useReportApi = () => ({
  getSummary: ReportService.getSummary,
  getPortfolio: ReportService.getPortfolio,
  getKpis: ReportService.getKpis,
  getCashFlow: ReportService.getCashFlow,
  getProfitability: ReportService.getProfitability,
  getDelinquency: ReportService.getDelinquency,
  getMonthlyCollection: ReportService.getMonthlyCollection,
  getActiveLoans: ReportService.getActiveLoans,
  getPaymentHistory: ReportService.getPaymentHistory,
  getAudit: ReportService.getAudit
})

// Components
import ReportsView from './View/ReportsView.vue'
export { ReportsView }

export type { ReportSummary, CashFlowParams } from './types/Report.Type'