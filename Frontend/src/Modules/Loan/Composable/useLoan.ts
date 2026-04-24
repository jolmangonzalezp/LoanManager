import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { LoanService } from '../Service/Loan.Service'
import { CustomerService } from '@/Modules/Customer/Service/Customer.Service'
import { usePagination } from '@/Shared/Composable/usePagination'
import { useAlert } from '@/Shared/Composable/useAlert'
import type {LoanForm, LoanReport, Loan} from '../types/Loan.Type'
import {CustomerName} from "../../Customer";

const loans = ref<Loan[]>([])
const loan = ref<Loan>()
const customers = ref<CustomerName[]>([])
const loanForm = ref<LoanForm>()
const report = ref<LoanReport>()

export function useLoan() {
  const route = useRoute()
  const alert = useAlert()

  const loading = ref(false)
  const error = ref<string | null>(null)


  const columns = [
    { key: 'loanNumber', label: 'No. Préstamo' },
    { key: 'partialName', label: 'Cliente' },
    { key: 'capital', label: 'Monto' },
    { key: 'status', label: 'Estado' },
    { key: 'progress', label: 'Progreso' }
  ]

  const { 
    page, 
    totalPages, 
    totalItems, 
    paginatedData, 
    setPage, 
    setData 
  } = usePagination(() => loans.value, 10)

  const totalCartera = computed(() => 
    loans.value.reduce((sum, l) => sum + l.capital, 0)
  )



  const emptyForm = ():void => {
    const hoy = new Date().toLocaleDateString('sv-SE', { timeZone: 'America/Bogota' });
    loanForm.value = {
      customer: "",
      capital: 0,
      interestRate: 0,
      dateStart: hoy
    }
  }

  const fillForm = ():void => {
    loanForm.value = {
      customer: loan.value.customer.id,
      capital: loan.value.capital,
      interestRate: loan.value.interestRate,
      dateStart: loan.value.startDate
    }
  }

  const fillFromCustomer = (customerId: string):void => {
    const hoy = new Date().toLocaleDateString('sv-SE', { timeZone: 'America/Bogota' });
    loanForm.value = {
      customer: customerId,
      capital: 0,
      interestRate: 0,
      dateStart: hoy
    }
  }


  const getAll = async (): Promise<void> => {
    try {
      loans.value = await LoanService.getAll()
    } catch (e: any) {
      throw e;
    }
  }

  const getById = async (id: string): Promise<void> => {
    try {
      loan.value = await LoanService.getById(id);
    }catch (e: any) {
      throw e;
    }
  }

  const getReport = async (): void => {
    try {
      report.value = await LoanService.getReport()
    } catch (e) {
      console.error(e)
    }
  }

  const getCustomers = async () => {
    try {
      customers.value = await CustomerService.getSummary()
    } catch (e) {
      console.error(e)
    }
  }

  const create = async (data: LoanForm):void => {
    try {
      await LoanService.create(data)
      await getAll()
    } catch (e: any) {
      throw e
    }
  }

  const update = async (id: string, data: LoanForm) => {
    loading.value = error.value = null
    try {
      await LoanService.update(id, data)
      await getAll()
    } catch (e: any) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  const processPayment = async (id: string, amount: number, payment_date: string) => {
    loading.value = error.value = null
    try {
      await LoanService.processPayment(id, amount, payment_date)
      await getAll()
    } catch (e: any) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  const getStatusLabel = (status: string): string => {
    const labels: Record<string, string> = {
      active: 'Activo',
      paid: 'Pagado',
      defaulted: 'Vencido'
    }
    return labels[status] || status
  }

  return {
    loading,
    error,
    loan,
    loans,
    loanForm,
    paginatedData,
    report,
    customers,
    columns,
    page,
    totalPages,
    totalItems,
    setPage,
    totalCartera,
    getAll,
    getById,
    getReport,
    getCustomers,
    create,
    update,
    processPayment,
    emptyForm,
    fillForm,
    fillFromCustomer,
    getStatusLabel,
  }
}