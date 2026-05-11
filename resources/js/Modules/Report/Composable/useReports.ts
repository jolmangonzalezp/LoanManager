import { ref } from 'vue'
import { CashFlowParams,
    CashFLowResponse, ReportDeliquency, ReportPortfolio, ReportProfitable, ReportService, ReportSummary } from '@/Modules/Report';
import { useNotifier } from '@/Shared';

const summary = ref<ReportSummary>();
const porfolioReport = ref<ReportPortfolio>();
const profitabilityReport = ref<ReportProfitable>();
const deliquencyReport = ref<ReportDeliquency>();
const cashFlowParams = ref<CashFlowParams>({
    fechaInicio: '',
    fechaFin: ''
});
const cashFlowResponse = ref<CashFLowResponse>()
const loanProfitable = ref<{
    capital: number
    customerName: string
    diasActivo: number
    interesesCobrados: number
    loanId: string
    loanNumber: string
    roi: number
}[]>([]);
const loanDelinquency = ref<{
    loanId: string
    loanNumber: string
    customerName: string
    saldoPendiente: number
    diasAtrazo: number
}[]>([]);


export function useReports() {

    const notify =  useNotifier();

    const columnsProfitable = [
        {key: 'loanNumber', label: 'Prestamo'},
        {key: 'customerName', label: 'Cliente'},
        {key: 'capital', label: 'Capital'},
        {key: 'interesesCobrados', label: 'Intereses'},
        {key: 'diasActivo', label: 'dias'},
        {key: 'roi', label: 'ROI'},
    ]

    const columnsDeliquency = [
        {key: 'loanNumber', label: 'Prestamo'},
        {key: 'customerName', label: 'Cliente'},
        {key: 'saldoPendiente', label: 'Saldo Pendiente'},
        {key: 'diasAtrazo', label: 'Dias de Atrazo'}
    ]
    const getSummary = async (): Promise<void>  =>{
        notify.loading("CARGANDO", "Cargando los datos");
        try {
            summary.value = await ReportService.getSummary();
        } catch (e: any) {
            notify.error("Error", e)
        } finally {
            notify.closeLoading();
        }
    }

    const getPortfolio = async (): Promise<void> => {
        notify.loading("CARGANDO", "Cargando los datos");
        try {
            porfolioReport.value = await ReportService.getPortfolio();
        }catch (e: any){
            notify.error("Error", e)
        }finally {
            notify.closeLoading();
        }
    }

    const getProfitability = async (): Promise<void> => {
        notify.loading("CARGANDO", "Cargando los datos");
        try {
            const r = await ReportService.getProfitability();
            loanProfitable.value = r.roiPorPrestamo;
            profitabilityReport.value = r;
        } catch (e: any) {
            notify.error("Error", e)
        }finally {
            notify.closeLoading();
        }
    }

    const getDeliquency = async (): Promise<void> => {
        notify.loading("CARGANDO", "Cargando los datos");
        try {
            const r = await ReportService.getDelinquency();
            loanDelinquency.value = r.detalleMora;
            console.log(loanDelinquency.value);
            deliquencyReport.value = r;
            console.log(deliquencyReport.value);
        }catch (e: any){
            notify.error("Error", e)
        }finally {
            notify.closeLoading();
        }
    }

    const cashFlow = async (params: CashFlowParams): Promise<void> => {
        notify.loading("CARGANDO", "Cargando los datos");
        try {
            cashFlowResponse.value = await ReportService.getCashFlow(params);
        } catch (e: any) {
            notify.error("Error", e)
        }finally {
            notify.closeLoading();
        }
    }

    const initCashFlow = ():void => {
        const hoy = new Date().toLocaleDateString('sv-SE', { timeZone: 'America/Bogota' });
        const newDate = new Date(hoy);
        newDate.setMonth(newDate.getMonth() -1);
        const last = newDate.toLocaleDateString('sv-SE', { timeZone: 'America/Bogota' });

        cashFlowParams.value = {
            fechaInicio: last,
            fechaFin: hoy
        }
    }

  return {
      summary,
      porfolioReport,
      profitabilityReport,
      deliquencyReport,
      cashFlowParams,
      cashFlowResponse,
      columnsProfitable,
      loanProfitable,
      columnsDeliquency,
      loanDelinquency,
      getSummary,
      getPortfolio,
      getProfitability,
      getDeliquency,
      cashFlow,
      initCashFlow,
  }
}
