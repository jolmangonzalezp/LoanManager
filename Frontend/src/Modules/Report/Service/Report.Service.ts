import { ReportApi } from '../Api/Report.Api'
import type {ReportSummary, CashFlowParams, CustomerKPI} from '../types/Report.Type'
import {ReportMapper} from "../Mapper/Report.Mapper";
import {CustomerKPIDTO} from "../types/reportDTO.Type";

export const ReportService = {
  async getSummary(): Promise<ReportSummary> {
    return ReportApi.getSummary()
  },

  async getPortfolio(): Promise<any> {
    return ReportApi.getPortfolio()
  },

  async getKpis(): Promise<any> {
    return ReportApi.getKpis()
  },

  async getCustomerKpi(): Promise<CustomerKPI> {
    const response:CustomerKPIDTO = await ReportApi.getCustomerKpi();
    return await ReportMapper.toCustomerKPI(response);
  },

  async getCashFlow(params: CashFlowParams): Promise<any> {
    return ReportApi.getCashFlow(params)
  },

  async getProfitability(): Promise<any> {
    return ReportApi.getProfitability()
  },

  async getDelinquency(): Promise<any> {
    return ReportApi.getDelinquency()
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