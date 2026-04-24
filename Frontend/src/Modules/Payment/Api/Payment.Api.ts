import httpClient from '@/Infrastructure/http/Client'
import {PaymentDTO, PaymentFormDTO} from "../types/PaymentDTO.Type";

const BASE = '/payments'

export const PaymentApi = {
  async getAll(): Promise<PaymentDTO[]> {
    return httpClient.get<PaymentDTO[]>(BASE)
  },

  async getById(id: string): Promise<PaymentDTO> {
    return httpClient.get<PaymentDTO>(`${BASE}/${id}`)
  },

  async getMonthlyReport(): Promise {
    return httpClient.get(`${BASE}/monthly-report`)
  },

  async create(data: PaymentFormDTO): Promise<string> {
    return httpClient.post<PaymentDTO>(BASE, data)
  }
}