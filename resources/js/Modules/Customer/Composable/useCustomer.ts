import { ref, computed } from 'vue'
import { Customer, CustomerForm, CustomerName, CustomerService, LoansByCustomer } from '@/Modules/Customer';
import { useMask, useModal, useNotifier } from '@/Shared';
import { CustomerKPI, ReportService } from '@/Modules/Report';


const customer = ref<Customer | null>(null)
const customers = ref<Customer[]>([])
const customerForm = ref<CustomerForm | null>(null)
const customerNames = ref<CustomerName[]>([])
const customerKPI = ref<CustomerKPI>()
const loans = ref<LoansByCustomer[]>([])

export function useCustomer() {
    const notify =  useNotifier();
  const modal = useModal();
  const mask = useMask();

  /******************/
  /*** Data state ***/
  /******************/




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


  //const { page,totalPages,totalItems,paginatedData,setPage,setData} = usePagination(() => customers.value, 6)

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
      if (!customer.value) return
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

  const getAll = async (): Promise<void> => {
      notify.loading("Cargando", "");
    try {
      const response =  await CustomerService.getAll();
      response.map(r => {
        r.name.firstName = mask.maskStart(r.name.firstName)
        r.name.middleName = mask.maskStart(r.name.middleName)
        r.name.lastName = mask.maskStart(r.name.lastName)
        r.name.secondLastName = mask.maskStart(r.name.secondLastName)
        r.partialName = mask.maskStart(r.partialName)
        r.email = mask.maskEmail(r.email || "")
        r.dni.number = mask.maskEnd(r.dni.number)
        r.fullDni = mask.maskEnd(r.fullDni)
        r.phone = mask.maskEnd(r.phone)
        r.address = mask.maskEnd(r.address)
      })
      customers.value = response;
    } catch (e: any) {
      notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const getById = async (id: string): Promise<void> => {
      notify.loading("Cargando", "");
    try {
        customer.value = await CustomerService.getById(id);
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const getLoans = async (id: string): Promise<void> => {
      notify.loading("Cargando", "");
    try {
        loans.value = await CustomerService.getLoans(id);
    }  catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const create = async (data: CustomerForm): Promise<void> => {
      notify.loading("Cargando", "");
    try {
      await CustomerService.create(data);
      await getCustomerKPI();
      await getAll();
      modal.close();
      notify.toastSuccess("Cliente se ha guardado exitosamente");
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const update = async (id: string, data: CustomerForm): Promise<void> => {
      notify.loading("Cargando", "");
    try {
      await CustomerService.update(id, data);
      await getAll();
      modal.close();
      notify.toastSuccess("Cliente se ha actualizado exitosamente");
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const remove = async (id: string): Promise<void> => {
      notify.loading("Cargando", "");
    try {
      await CustomerService.delete(id);
      await getAll();
      notify.toastSuccess("Cliente se ha eliminado exitosamente");
    } catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  const getCustomerKPI = async ():Promise<void> => {
      notify.loading("Cargando", "");
    try {
      customerKPI.value = await ReportService.getCustomerKpi();
    }catch (e: any) {
        notify.error("Error", e.message)
    } finally {
        notify.closeLoading()
    }
  }

  return {
    customer,
    customers,
    customerForm,
    customerNames,
    customerKPI,
    loans,
    columns,
    summary,
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
