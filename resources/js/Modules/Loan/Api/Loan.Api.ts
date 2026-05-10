import { Http } from '@/Infrastructure';
import { LoanDTO, LoanFormDTO, LoanReportDTO } from '@/Modules/Loan';

const BASE = '/loans'

export const LoanApi = {
    async getAll(): Promise<LoanDTO[]> {
        return await Http.get<LoanDTO[]>(BASE);
    },

    async getById(id: string): Promise<LoanDTO> {
        return Http.get<LoanDTO>(`${BASE}/${id}`)
    },

    async getReport(): Promise<LoanReportDTO> {
        return Http.get<LoanReportDTO>(`${BASE}/report`)
    },

    async create(data: LoanFormDTO): Promise<boolean> {
        return  await Http.post<boolean>(BASE, data);
    },

    async update(id: string, data: LoanFormDTO): Promise<boolean> {
        return Http.put<boolean>(`${BASE}/${id}`, data)
    },

    async delete(id: string): Promise<void> {
        return Http.delete(`${BASE}/${id}`)
    },

    async processPayment(id: string, amount: number, payment_date: string): Promise<LoanDTO> {
        return Http.post<LoanDTO>(`${BASE}/${id}/payment`, { amount, payment_date })
    }
}
