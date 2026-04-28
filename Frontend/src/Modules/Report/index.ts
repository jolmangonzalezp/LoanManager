import { ReportService } from './Service/Report.Service'
import { useReports } from './Composable/useReports'
import { useReports as useReportsView } from './Composable/useReports'
import { ReportApi } from './Api/Report.Api'

export { ReportApi }
export { ReportService }
export { useReports }
export { useReportsView }

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

import ReportsView from './View/ReportsView.vue'
export { ReportsView }

export type { ReportSummary, CashFlowParams } from './types/Report.Type'