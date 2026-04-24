import httpClient from '@/Infrastructure/http/Client'
import { LoanDTO, LoanFormDTO, LoanReportDTO } from '../types/LoanDTO.Type'

const BASE = '/loans'

export const LoanApi = {
  async getAll(): Promise<LoanDTO[]> {
    return await httpClient.get<LoanDTO[]>(BASE);
  },

  async getById(id: string): Promise<LoanDTO> {
    return httpClient.get<LoanDTO>(`${BASE}/${id}`)
  },

  async getReport(): Promise<LoanReportDTO> {
    return httpClient.get<LoanReportDTO>(`${BASE}/report`)
  },

  async create(data: LoanFormDTO): Promise<string> {
    return httpClient.post<string>(BASE, data)
  },

  async update(id: string, data: LoanFormDTO): Promise<string> {
    return httpClient.put<LoanDTO>(`${BASE}/${id}`, data)
  },

  async delete(id: string): Promise<void> {
    return httpClient.delete(`${BASE}/${id}`)
  },

  async processPayment(id: string, amount: number, payment_date: string): Promise<LoanDTO> {
    return httpClient.post<LoanDTO>(`${BASE}/${id}/payment`, { amount, payment_date })
  }
}