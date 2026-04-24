import { ref, onMounted } from 'vue'
import { ReportService } from '@/Modules/Report/Service/Report.Service'
import type { ReportSummary } from '@/Modules/Report/types/Report.Type'

export function useDashboard() {
  const loading = ref(false)
  const error = ref<string | null>(null)
  const summary = ref<ReportSummary | null>(null)

  const getSummary = async () => {
    loading.value = true
    error.value = null
    try {
      summary.value = await ReportService.getSummary()
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const navigateToLoans = () => {
    window.location.href = '/prestamos'
  }

  onMounted(() => {
    getSummary()
  })

  return {
    loading,
    error,
    summary,
    getSummary,
    navigateToLoans
  }
}

export function useReport() {
  const loading = ref(false)
  const error = ref<string | null>(null)

  const getSummary = async () => {
    loading.value = true
    try {
      return await ReportService.getSummary()
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const getPortfolio = async () => {
    loading.value = true
    try {
      return await ReportService.getPortfolio()
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const getProfitability = async () => {
    loading.value = true
    try {
      return await ReportService.getProfitability()
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const getDelinquency = async () => {
    loading.value = true
    try {
      return await ReportService.getDelinquency()
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const getCashFlow = async (params: any) => {
    loading.value = true
    try {
      return await ReportService.getCashFlow(params)
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const getActiveLoans = async () => {
    loading.value = true
    try {
      return await ReportService.getActiveLoans()
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const getPaymentHistory = async (params?: any) => {
    loading.value = true
    try {
      return await ReportService.getPaymentHistory(params)
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const getAudit = async (params?: any) => {
    loading.value = true
    try {
      return await ReportService.getAudit(params)
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    getSummary,
    getPortfolio,
    getProfitability,
    getDelinquency,
    getCashFlow,
    getActiveLoans,
    getPaymentHistory,
    getAudit
  }
}