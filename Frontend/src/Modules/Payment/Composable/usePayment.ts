import { ref, onMounted } from 'vue'
import { PaymentService } from '../Service/Payment.Service'
import { useModalState } from '@/Shared'
import { usePagination } from '@/Shared/Composable/usePagination'
import type {Payment, PaymentForm} from '../types/Payment.Type'

const payments = ref<Payment[]>([])
const payment = ref<Payment>()
const paymentForm = ref<PaymentForm>()
const monthlyReport = ref(null)

export function usePayment() {
  const loading = ref(false)
  const error = ref<string | null>(null)
  const loans = ref<any[]>([])

  const columns = [
    { key: 'paymentDate', label: 'Fecha' },
    { key: 'loanNumber', label: 'Préstamo' },
    { key: 'amount', label: 'Monto' },
    { key: 'status', label: 'Estado' }
  ]

  const { 
    page, 
    totalPages, 
    totalItems, 
    paginatedData, 
    setPage, 
    setData 
  } = usePagination(() => payments.value, 10)

  const { isOpen: showForm, open: openForm, close: closeForm } = useModalState()
  const { isOpen: showDetail, open: openDetail, close: closeDetail } = useModalState()

  const selected = ref<any>(null)

  const initForm = (id: string, remain: number, interest: number): void => {
    const hoy = new Date().toLocaleDateString('sv-SE', { timeZone: 'America/Bogota' });
    const minPay = remain * (interest / 100);
    paymentForm.value = {
      loanId: id,
      amount: minPay,
      paymentDate: hoy
    }
  }

  const fillForm = (): void => {
    paymentForm.value = {
      loanId: payment.value.loan.id,
      amount: payment.value.amount,
      paymentDate: payment.value.paymentDate,
    }
  }

  const getAll = async () => {
    try {
      payments.value = await PaymentService.getAll()

    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const getById = async (id: string): void => {
    try {
      payment.value = await PaymentService.getById(id);
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const getMonthlyReport = async () => {
    try {
      monthlyReport.value = await PaymentService.getMonthlyReport()
      console.log(monthlyReport.value)
    } catch (e) {
      console.error(e)
    }
  }
  const create = async (data: PaymentForm): void => {
    try {
      await PaymentService.create(data)
      await Promise.all([getAll(), getMonthlyReport()])
    } catch (e: any) {
      throw e
    }
  }

  const update = async (id:string, data: PaymentForm): void => {
    try {
      console.log(data)
    } catch (e: any) {
      throw e
    }
  }


  return {
    payments,
    payment,
    paymentForm,
    paginatedData,
    monthlyReport,
    loans,
    columns,
    page,
    totalPages,
    totalItems,
    setPage,
    showForm,
    showDetail,
    selected,
    getAll,
    getById,
    getMonthlyReport,
    create,
    update,
    initForm,
    fillForm,
  }
}