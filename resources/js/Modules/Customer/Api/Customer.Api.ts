import { Http } from '@/Infrastructure';
import { CustomerDTO, CustomerFormDTO, CustomerNameDTO, LoansByCustomerDTO } from '@/Modules/Customer';

const BASE = '/customers'

export const CustomerApi = {

    async getAll(): Promise<CustomerDTO[]> {
        return await Http.get<CustomerDTO[]>(BASE);
    },

    async getById(id: string): Promise<CustomerDTO> {
        return await Http.get<CustomerDTO>(`${BASE}/${id}`);
    },

    async getSummary(): Promise<CustomerNameDTO[]> {
        return  await Http.get<CustomerNameDTO[]>(`${BASE}/summary`)
    },

    async getLoans(id: string): Promise<LoansByCustomerDTO[]> {
        return  await Http.get<LoansByCustomerDTO[]>(`${BASE}/${id}/loans`);
    },

    async create(data: CustomerFormDTO): Promise<boolean> {
        return  await Http.post<boolean>(BASE, data);
    },

    async update(id: string, data: CustomerFormDTO): Promise<boolean> {
        return Http.put<boolean>(`${BASE}/${id}`, data)
    },

    async delete(id: string): Promise<void> {
        return Http.delete(`${BASE}/${id}`)
    }
}
