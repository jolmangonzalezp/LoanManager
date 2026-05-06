import { CashFlowParams,
    CashFlowParamsDTO, CashFLowResponse,
    CashFLowResponseDTO, CustomerKPI, CustomerKPIDTO, ReportApi, ReportDeliquencyDTO, ReportMapper,
    ReportPortfolio, ReportSummary, ReportSummaryDTO } from '@/Modules/Report';
import { ReportDeliquency, ReportProfitable } from '@/Modules/Report/types/Report.Type.ts';
import { ReportProfitableDTO } from '@/Modules/Report/types/reportDTO.Type.ts';

export const ReportService = {
  async getSummary(): Promise<ReportSummary> {
    const response:ReportSummaryDTO = await ReportApi.getSummary();
    return ReportMapper.toReportSummary(response);
  },

  async getPortfolio(): Promise<ReportPortfolio> {
    const r = await ReportApi.getPortfolio()
    return ReportMapper.toPortfolio(r);
  },

    async getProfitability(): Promise<ReportProfitable> {
        const r: ReportProfitableDTO = await ReportApi.getProfitability();
        return ReportMapper.toProfitable(r);
    },

    async getDelinquency(): Promise<ReportDeliquency> {
        const r: ReportDeliquencyDTO = await ReportApi.getDelinquency()
        return ReportMapper.toDeliquency(r);
    },

  async getKpis(): Promise<any> {
    return ReportApi.getKpis()
  },

  async getCustomerKpi(): Promise<CustomerKPI> {
    const response:CustomerKPIDTO = await ReportApi.getCustomerKpi();
    return ReportMapper.toCustomerKPI(response);
  },

  async getCashFlow(params: CashFlowParams): Promise<CashFLowResponse> {
      const mapped: CashFlowParamsDTO = ReportMapper.toCashFLowParamsDTO(params);
      const r: CashFLowResponseDTO = await ReportApi.getCashFlow(mapped);
      return ReportMapper.toCashFlowResponse(r);
  },

  async getMonthlyCollection(params?: any): Promise<any> {
    return ReportApi.getMonthlyCollection(params)
  },

  async getActiveLoans(): Promise<any> {
    return ReportApi.getActiveLoans()
  },

  async getPaymentHistory(params?: any): Promise<any> {
    return ReportApi.getPaymentHistory(params)
  },

  async getAudit(params?: any): Promise<any> {
    return ReportApi.getAudit(params)
  }
}
