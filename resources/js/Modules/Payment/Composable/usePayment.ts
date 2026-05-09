import { ref, onMounted } from 'vue'
import { Payment, PaymentForm, PaymentReport, PaymentService } from '@/Modules/Payment';
import { useNotifier } from '@/Shared';
import { useRouter } from 'vue-router';


const payments = ref<Payment[]>([])
const payment = ref<Payment>()
const paymentForm = ref<PaymentForm>()
const monthlyReport = ref<PaymentReport>()

export function usePayment() {
    const notify =  useNotifier();
    const router = useRouter();
    const loans = ref<any[]>([])

    const columns = [
        { key: 'paymentDate', label: 'Fecha' },
        { key: 'loanNumber', label: 'Préstamo' },
        { key: 'amount', label: 'Monto' },
        { key: 'status', label: 'Estado' }
    ]

    //const { page, totalPages, totalItems, paginatedData, setPage, setData } = usePagination(() => payments.value, 10)

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
        if (!payment.value) return
        paymentForm.value = {
            loanId: payment.value.loan.id,
            amount: payment.value.amount,
            paymentDate: payment.value.paymentDate,
        }
    }

    const getAll = async (): Promise<void> => {
        notify.loading("Cargando", "");
        try {
            payments.value = await PaymentService.getAll()
            notify.closeLoading();
        } catch (e: any) {
            notify.closeLoading();
            notify.error("Error", e)
        }
    }

    const getById = async (id: string): Promise<void> => {
        notify.loading("Cargando", "");
        try {
            payment.value = await PaymentService.getById(id);
            notify.closeLoading();
        } catch (e: any) {
            notify.closeLoading();
            notify.error("Error", e)
        }
    }

    const getMonthlyReport = async (): Promise<void> => {
        notify.loading("Cargando", "");
        try {
            monthlyReport.value = await PaymentService.getMonthlyReport()
        } catch (e: any) {
            notify.error("Error", e.message)
        } finally {
            notify.closeLoading()
        }

    }
    const create = async (data: PaymentForm): Promise<void> => {
        notify.loading("Cargando", "");
        try {
            await PaymentService.create(data)
            await Promise.all([getAll(), getMonthlyReport()])
            notify.closeLoading();
            notify.success("Exito", "Pago se ha guardado exitosamente");
        } catch (e: any) {
            notify.closeLoading();
            notify.toastError(e.message);
        }
    }

    const update = async (id:string, data: PaymentForm): Promise<void> => {
        notify.loading("Cargando", "");
        try {
            await PaymentService.update(id, data);
            getAll()
            notify.success("Exito", "Pago se ha actualizado exitosamente");
        } catch (e: any) {
            notify.toastError(e.message);
        }
    }

    return {
        payments,
        payment,
        paymentForm,
        monthlyReport,
        loans,
        columns,
        getAll,
        getById,
        getMonthlyReport,
        create,
        update,
        initForm,
        fillForm,
    }
}
