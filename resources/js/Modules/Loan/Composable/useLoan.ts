import { ref, computed } from 'vue'
import { Loan, LoanForm, LoanReport, LoanService } from '@/Modules/Loan';
import { CustomerName, CustomerService } from '@/Modules/Customer';
import { formatCurrency, useMask, useNotifier } from '@/Shared';
import { useRouter } from 'vue-router';
//import { usePagination } from '@/Shared/Composable/usePagination'

const loans = ref<Loan[]>([])
const loan = ref<Loan>()
const customers = ref<CustomerName[]>([])
const loanForm = ref<LoanForm>()
const report = ref<LoanReport>()

export function useLoan() {
    const notify =  useNotifier();
    const router = useRouter();

    const columns = [
        { key: 'loanNumber', label: 'No. Préstamo' },
        { key: 'partialName', label: 'Cliente' },
        { key: 'capital', label: 'Monto' },
        { key: 'status', label: 'Estado' },
        { key: 'progress', label: 'Progreso' }
    ]

    //const { page, totalPages, totalItems, paginatedData, setPage, setData } = usePagination(() => loans.value, 10)

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
        if (!loan.value) return
        loanForm.value = {
            customer: loan.value.customer.id,
            capital: Number(loan.value.capital),
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
        notify.loading("Cargando", "");
        try {
            const response = await LoanService.getAll()
            response.map(r => {
                r.partialName = useMask().maskStart(r.partialName)
                r.progress = r.progress ? Number(r.progress.toFixed(2)) : 0
                r.capital = r.capital ? formatCurrency(r.capital) : formatCurrency(0)
            })
            loans.value = response
            notify.closeLoading();
        } catch (e: any) {
            notify.closeLoading();
            notify.error("Error", e.message);
        }
    }

    const getById = async (id: string): Promise<void> => {
        notify.loading("Cargando", "");
        try {
            loan.value = await LoanService.getById(id);
            loan.value.partialName = useMask().maskStart(loan.value.partialName)
            loan.value.progress = loan.value.progress ? Number(loan.value.progress.toFixed(2)) : 0
            notify.closeLoading();
        } catch (e: any) {
            notify.closeLoading();
            notify.error("Error", e.message);
        }
    }

    const getReport = async (): Promise<void> => {
        notify.loading("Cargando", "");
        try {
            report.value = await LoanService.getReport()
            notify.closeLoading();
        } catch (e: any) {
            notify.closeLoading();
            notify.error("Error", e.message);
        }
    }

    const getCustomers = async () => {
        notify.loading("Cargando", "");
        try {
            customers.value = await CustomerService.getSummary();
            notify.closeLoading();
        } catch (e: any) {
            notify.closeLoading();
            notify.error("Error", e.message);
        }
    }

    const create = async (data: LoanForm): Promise<boolean> => {
        notify.loading("Cargando", "");
        try {
            console.log(data);
            const response = await LoanService.create(data);
            if (response) {
                await getAll();
                await getReport();
            }
            notify.closeLoading()
            notify.success("Exito", "Prestamo se ha guardado exitosamente");
            await router.push('/prestamos');
            return response;
        } catch (e: any) {
            notify.closeLoading();
            notify.toastError(e.message);
        }
    }

    const update = async (id: string, data: LoanForm): Promise<boolean> => {
        notify.loading("Cargando", "");
        try {
            const response = await LoanService.update(id, data)
            if (response) {
                await getAll();
                await getReport();
            }
            notify.closeLoading();
            notify.success("Exito", "Prestamo se ha actualizado exitosamente");
            await router.push('/prestamos')
            return response;
        } catch (e: any) {
            notify.closeLoading();
            notify.toastError(e.message);
        }
    }

    const processPayment = async (id: string, amount: number, payment_date: string): Promise<void> => {
        notify.loading("Cargando", "");
        try {
            await LoanService.processPayment(id, amount, payment_date)
            await getAll()
            notify.closeLoading();
            notify.success("Exito", "Pago se ha procesado exitosamente");
        } catch (e: any) {
            notify.closeLoading();
            notify.error("Error", e.message);
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
        loan,
        loans,
        loanForm,
        report,
        customers,
        columns,
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
