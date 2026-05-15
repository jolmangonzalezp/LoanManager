import { Http } from '@/Infrastructure';
import { CashFlowParamsDTO,
    CashFLowResponseDTO, CustomerKPIDTO, ReportDeliquencyDTO, ReportPortfolioDTO, ReportProfitableDTO, ReportSummaryDTO } from '@/Modules/Report';

const BASE = '/reports'

export const ReportApi = {
  async getSummary(): Promise<ReportSummaryDTO> {
    return  await Http.get(`${BASE}/summary`);
  },

  async getPortfolio(): Promise<ReportPortfolioDTO> {
    return Http.get<ReportPortfolioDTO>(`${BASE}/portfolio`)
  },

    async getProfitability(): Promise<ReportProfitableDTO> {
        return await Http.get<ReportProfitableDTO>(`${BASE}/profitability`);
    },

    async getDelinquency(): Promise<ReportDeliquencyDTO> {
        return await Http.get<ReportDeliquencyDTO>(`${BASE}/delinquency`)
    },

    async getKpis(): Promise<any> {
    return Http.get(`${BASE}/kpis`)
  },

  async getCustomerKpi(): Promise<CustomerKPIDTO> {
    return Http.get<CustomerKPIDTO>(`${BASE}/kpis/customers`)
  },

  async getCashFlow(params: CashFlowParamsDTO): Promise<CashFLowResponseDTO> {
    return Http.get<CashFLowResponseDTO>(`${BASE}/cash-flow`, params)
  },

  async getMonthlyCollection(params?: any): Promise<any> {
    return Http.get(`${BASE}/monthly-collection`, params)
  },

  async getActiveLoans(): Promise<any> {
    return Http.get(`${BASE}/active-loans`)
  },

  async getPaymentHistory(params?: any): Promise<any> {
    return Http.get(`${BASE}/payment-history`, params)
  },

  async getAudit(params?: any): Promise<any> {
    return Http.get(`${BASE}/audit`, params)
  }
}
