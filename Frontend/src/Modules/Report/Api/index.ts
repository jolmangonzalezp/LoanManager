import { useApi } from '@/Shared/Composable/useApi'

export function useReportApi() {
  const api = useApi()

  async function getSummary() {
    return api.get('/reports/summary')
  }

  async function getPortfolio() {
    return api.get('/reports/portfolio')
  }

  async function getKpis() {
    return api.get('/reports/kpis')
  }

  async function getCashFlow(params) {
    return api.get('/reports/cash-flow', params)
  }

  async function getProfitability() {
    return api.get('/reports/profitability')
  }

  async function getDelinquency() {
    return api.get('/reports/delinquency')
  }

  async function getMonthlyCollection(params) {
    return api.get('/reports/monthly-collection', params)
  }

  async function getActiveLoans() {
    return api.get('/reports/active-loans')
  }

  async function getPaymentHistory(params) {
    return api.get('/reports/payment-history', params)
  }

  async function getAudit(params) {
    return api.get('/reports/audit', params)
  }

  return {
    loading: api.loading,
    error: api.error,
    getSummary,
    getPortfolio,
    getKpis,
    getCashFlow,
    getProfitability,
    getDelinquency,
    getMonthlyCollection,
    getActiveLoans,
    getPaymentHistory,
    getAudit
  }
}
