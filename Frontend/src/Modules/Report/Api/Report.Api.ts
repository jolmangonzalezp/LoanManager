import httpClient from '@/Infrastructure/http/Client'
import type { ReportSummary, CashFlowParams } from '../types/Report.Type'
import {CustomerKPIDTO} from "../types/reportDTO.Type";

const BASE = '/reports'

export const ReportApi = {
  async getSummary(): Promise<ReportSummary> {
    return httpClient.get<ReportSummary>(`${BASE}/summary`)
  },

  async getPortfolio(): Promise<any> {
    return httpClient.get(`${BASE}/portfolio`)
  },

  async getKpis(): Promise<any> {
    return httpClient.get(`${BASE}/kpis`)
  },

  async getCustomerKpi(): Promise<CustomerKPIDTO> {
    return httpClient.get<CustomerKPIDTO>(`${BASE}/kpis/customers`)
  },

  async getCashFlow(params: CashFlowParams): Promise<any> {
    return httpClient.get(`${BASE}/cash-flow`, params)
  },

  async getProfitability(): Promise<any> {
    return httpClient.get(`${BASE}/profitability`)
  },

  async getDelinquency(): Promise<any> {
    return httpClient.get(`${BASE}/delinquency`)
  },

  async getMonthlyCollection(params?: any): Promise<any> {
    return httpClient.get(`${BASE}/monthly-collection`, params)
  },

  async getActiveLoans(): Promise<any> {
    return httpClient.get(`${BASE}/active-loans`)
  },

  async getPaymentHistory(params?: any): Promise<any> {
    return httpClient.get(`${BASE}/payment-history`, params)
  },

  async getAudit(params?: any): Promise<any> {
    return httpClient.get(`${BASE}/audit`, params)
  }
}