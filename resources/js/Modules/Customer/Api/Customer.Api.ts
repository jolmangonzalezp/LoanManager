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

  async create(data: CustomerFormDTO): Promise<string> {
    const r = await Http.post<string>(BASE, data);
      console.log(r);
      return r;
  },

  async update(id: string, data: CustomerFormDTO): Promise<string> {
    return Http.put<string>(`${BASE}/${id}`, data)
  },

  async delete(id: string): Promise<void> {
    return Http.delete(`${BASE}/${id}`)
  }
}
