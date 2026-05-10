import { ref, onMounted } from 'vue'
import { Payment, PaymentForm, PaymentReport, PaymentService } from '@/Modules/Payment';
import { formatCurrency, useNotifier } from '@/Shared';
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
            paymentDate: hoy,
            paymentMethod: 'cash',
        }
    }

    const fillForm = (): void => {
        if (!payment.value) return
        paymentForm.value = {
            loanId: payment.value.loan.id,
            amount: Number(payment.value.amount),
            paymentDate: payment.value.paymentDate,
            paymentMethod: payment.value.paymentMethod,
        }
    }

    const getAll = async (): Promise<void> => {
        notify.loading("Cargando", "");
        try {
            const response = await PaymentService.getAll()
            response.map(pay => {
                pay.amount = pay.amount ? formatCurrency(pay.amount) : formatCurrency(0)
            });
            payments.value = response
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
    const create = async (data: PaymentForm): Promise<boolean> => {
        notify.loading("Cargando", "");
        try {
            const response = await PaymentService.create(data);
            if (response) {
                await Promise.all([getAll(), getMonthlyReport()])
            }
            notify.closeLoading();
            notify.success("Exito", "Pago se ha guardado exitosamente");
            return response;
        } catch (e: any) {
            notify.closeLoading();
            notify.toastError(e.message);
        }
    }

    const update = async (id:string, data: PaymentForm): Promise<boolean> => {
        notify.loading("Cargando", "");
        try {
            const response = await PaymentService.update(id, data);
            if (response) {
                await Promise.all([getAll(), getMonthlyReport()])
            }
            notify.success("Exito", "Pago se ha actualizado exitosamente");
            return response;
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
