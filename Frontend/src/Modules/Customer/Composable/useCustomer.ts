import { ref, computed } from 'vue'
// Services
import { CustomerService } from '../Service/Customer.Service'
// Composables
import { useModalState } from '@/Shared'
import { usePagination } from '@/Shared/Composable/usePagination'
import { useAlert } from '@/Shared/Composable/useAlert'
// Types
import type {Customer, CustomerForm, CustomerName, LoansByCustomer} from '../types/Customer.Type'
import {useModal} from "../../../Shared";
import CustomerDetailComponent from "../Component/CustomerDetailComponent.vue";
import {ReportService} from "../../Report";
import {CustomerKPI} from "../../Report/types/Report.Type";
import {useMask} from "../../../Shared/Composable/useMask";


const customer = ref<Customer | null>(null)
const customers = ref<Customer[]>([])
const customerForm = ref<CustomerForm>(null)
const customerNames = ref<CustomerName[]>([])
const customerKPI = ref<CustomerKPI>()
const loans = ref<LoansByCustomer[]>([])

export function useCustomer() {
  const alert = useAlert()
  const modal = useModal()
  const mask = useMask()

  /******************/
  /*** Data state ***/
  /******************/

  const loading = ref(false)
  const error = ref<string | null>(null)
  
  

  const detailLoading = ref(false)
  const detailCustomer = ref<Customer | null>(null)
  const detailLoans = ref<any[]>([])

  /***************************/
  /*** Settings y UI state ***/
  /***************************/

  const columns = [
    { key: 'partialName', label: 'Nombre Completo' },
    { key: 'fullDni', label: 'Cédula' },
    { key: 'phone', label: 'Teléfono' },
    { key: 'address', label: 'Dirección' }
  ]

  const { isOpen: showForm, open: openForm, close: closeForm } = useModalState()
  const { isOpen: showDetail, open: openDetail, close: closeDetail } = useModalState()

  const { 
    page, 
    totalPages, 
    totalItems, 
    paginatedData, 
    setPage, 
    setData 
  } = usePagination(() => customers.value, 6)

  const summary = computed(() => ({
    total: customers.value.length,
    active: 0,
    inactive: customers.value.length
  }))

  const emptyCustomer = () => {
    customerForm.value = {
      name: {
        firstName: "",
        middleName: "",
        lastName: "",
        secondLastName: "",
      },
      dni: {
        type: "",
        number: "",
      },
      phone: "",
      address: "",
      email: ""
    }
  }

  const fillCustomer = () => {
    customerForm.value = {
      name: {
        firstName: customer.value.name.firstName,
        middleName: customer.value.name.middleName,
        lastName: customer.value.name.lastName,
        secondLastName: customer.value.name.secondLastName,
      },
      dni: {
        type: customer.value.dni.type,
        number: customer.value.dni.number,
      },
      phone: customer.value.phone,
      address: customer.value.address,
      email: customer.value.email
    }
  }

  /*******************/
  /*** API actions ***/
  /*******************/

  const getAll = async (): void => {
    try {
      const response =  await CustomerService.getAll()
      response.map(r => {
        r.name.firstName = mask.maskStart(r.name.firstName)
        r.name.middleName = mask.maskStart(r.name.middleName)
        r.name.lastName = mask.maskStart(r.name.lastName)
        r.name.secondLastName = mask.maskStart(r.name.secondLastName)
        r.partialName = mask.maskStart(r.partialName)
        r.email = mask.maskEmail(r.email)
        r.dni.number = mask.maskEnd(r.dni.number)
        r.fullDni = mask.maskEnd(r.fullDni)
        r.phone = mask.maskEnd(r.phone)
        r.address = mask.maskEnd(r.address)
      })
      customers.value = response

      alert.loading("Cargando", "Cargando datos")
    } catch (e: any) {
      error.value = e.message || 'Error al cargar clientes'
    } finally {
      alert.close()
    }
  }

  const getById = async (id: string): void => {
    loading.value = true
    try {
      const response = await CustomerService.getById(id);
      response.name.firstName = mask.maskStart(response.name.firstName)
      response.name.middleName = mask.maskStart(response.name.middleName)
      response.name.lastName = mask.maskStart(response.name.lastName)
      response.name.secondLastName = mask.maskStart(response.name.secondLastName)
      response.email = mask.maskEmail(response.email)
      response.dni.number = mask.maskEnd(response.dni.number)
      response.phone = mask.maskEnd(response.phone)
      response.address = mask.maskEnd(response.address)
      customer.value = response
    } catch (e: any) {
      error.value = e.message || 'Error al cargar cliente'
    } finally {
      alert.close()
    }
  }

  const getLoans = async (id: string): void => {
    loans.value = await CustomerService.getLoans(id);
  }

  const create = async (data: CustomerForm): void => {
    try {
      await CustomerService.create(data);
      await getCustomerKPI();
      await getAll();
      modal.close();

    } catch (e: any) {
      throw e
    } finally {
      loading.value = false
    }
  }

  const update = async (id: string, data: CustomerForm): void => {
    loading.value = error.value = null
    try {
      const response = await CustomerService.update(id, data)

      await getAll()
      modal.close()

    } catch (e: any) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  const remove = async (id: string) => {
    loading.value = error.value = null
    try {
      await CustomerService.delete(id)
      await getAll()
    } catch (e: any) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  const getCustomerKPI = async ():void => {
    try {
      customerKPI.value = await ReportService.getCustomerKpi();
    }catch (e: any) {
      console.error(e)
    }
  }

  return {
    loading,
    error,
    customer,
    customers,
    customerForm,
    customerNames,
    customerKPI,
    loans,
    paginatedData,
    columns,
    page,
    totalPages,
    totalItems,
    setPage,
    summary,
    showForm,
    showDetail,
    detailCustomer,
    detailLoans,
    detailLoading,
    getAll,
    getById,
    create,
    update,
    remove,
    getCustomerKPI,
    getLoans,
    emptyCustomer,
    fillCustomer,
  }
}