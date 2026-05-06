import { ref } from 'vue'
import { ReportService, ReportSummary } from '@/Modules/Report';
import { useNotifier } from '@/Shared';

export function useDashboard() {
    const notify =  useNotifier();
  const summary = ref<ReportSummary | null>(null);

  const getSummary = async () => {
      notify.loading("Cargando", "");
    try {
      summary.value = await ReportService.getSummary();
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const navigateToLoans = () => {
    window.location.href = '/prestamos'
  }

  return {
    summary,
    getSummary,
    navigateToLoans
  }
}

export function useReport() {
    const notify =  useNotifier();

  const getSummary = async () => {
      notify.loading("Cargando", "");
    try {
      return await ReportService.getSummary()
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const getPortfolio = async () => {
      notify.loading("Cargando", "");
    try {
      return await ReportService.getPortfolio()
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const getProfitability = async () => {
      notify.loading("Cargando", "");
    try {
      return await ReportService.getProfitability()
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const getDelinquency = async () => {
      notify.loading("Cargando", "");
    try {
      return await ReportService.getDelinquency()
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const getCashFlow = async (params: any) => {
      notify.loading("Cargando", "");
    try {
      return await ReportService.getCashFlow(params)
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const getActiveLoans = async () => {
      notify.loading("Cargando", "");
    try {
      return await ReportService.getActiveLoans()
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const getPaymentHistory = async (params?: any) => {
      notify.loading("Cargando", "");
    try {
      return await ReportService.getPaymentHistory(params)
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const getAudit = async (params?: any) => {
      notify.loading("Cargando", "");
    try {
      return await ReportService.getAudit(params)
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  return {
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
