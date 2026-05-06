import { ReportService } from '@/Modules/Report/Service/Report.Service.ts';

export { useReports } from './Composable/useReports.ts';
export { ReportService } from './Service/Report.Service.ts';
export { ReportMapper } from './Mapper/Report.Mapper.ts';
export { ReportApi } from './Api/Report.Api.ts';


export const useReportApi = () => ({
  getSummary: ReportService.getSummary(),
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

export type {
    ReportSummary, CashFlowParams, CustomerKPI, ReportPortfolio, ReportProfitable, ReportDeliquency, CashFLowResponse
} from './types/Report.Type.ts';
export type {
    CustomerKPIDTO, ReportSummaryDTO, ReportPortfolioDTO, ReportProfitableDTO, ReportDeliquencyDTO, CashFlowParamsDTO, CashFLowResponseDTO
} from './types/reportDTO.Type.ts';

export { default as SummaryReport } from './View/reports/SummaryReport.vue';
export { default as PortfolioReport } from './View/reports/PortfolioReport.vue';
export { default as ProfitbilityReport } from './View/reports/ProfitabilityReport.vue';
export { default as DelinquencyReport } from './View/reports/DelinquencyReport.vue';
export { default as CashFlow } from './View/reports/CashFlowReport.vue';
export { default as ReportPage } from './View/ReportsView.vue';
