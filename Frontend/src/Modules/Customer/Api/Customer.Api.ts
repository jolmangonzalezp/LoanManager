import httpClient from '@/Infrastructure/http/Client'
import {Customer} from "../types/Customer.Type";
import {CustomerDTO, CustomerFormDTO, CustomerNameDTO, LoansByCustomerDTO} from "../types/CustomerDTO.Type";

const BASE = '/customers'

export const CustomerApi = {

  async getAll(): Promise<CustomerDTO[]> {
    return await httpClient.get<CustomerDTO[]>(BASE);
  },

  async getById(id: string): Promise<CustomerDTO> {
    return await httpClient.get<CustomerDTO>(`${BASE}/${id}`);
  },

  async getSummary(): Promise<CustomerNameDTO[]> {
    return  await httpClient.get<CustomerNameDTO[]>(`${BASE}/summary`)
  },

  async getLoans(id: string): Promise<LoansByCustomerDTO[]> {
    return  await httpClient.get<LoansByCustomerDTO[]>(`${BASE}/${id}/loans`);
  },

  async create(data: CustomerFormDTO): Promise<string> {
    return httpClient.post<Customer>(BASE, data)
  },

  async update(id: string, data: CustomerFormDTO): Promise<Customer> {
    return httpClient.put<Customer>(`${BASE}/${id}`, data)
  },

  async delete(id: string): Promise<void> {
    return httpClient.delete(`${BASE}/${id}`)
  }
}