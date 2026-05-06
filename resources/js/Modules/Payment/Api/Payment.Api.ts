import { Http } from '@/Infrastructure';
import { PaymentDTO, PaymentFormDTO, PaymentReportDTO } from '@/Modules/Payment';

const BASE = '/payments'

export const PaymentApi = {
  async getAll(): Promise<PaymentDTO[]> {
    return Http.get<PaymentDTO[]>(BASE)
  },

  async getById(id: string): Promise<PaymentDTO> {
    return Http.get<PaymentDTO>(`${BASE}/${id}`)
  },

  async getMonthlyReport(): Promise<PaymentReportDTO> {
    return Http.get<PaymentReportDTO>(`${BASE}/monthly`)
  },

  async create(data: PaymentFormDTO): Promise<string> {
    return Http.post<string>(BASE, data)
  }
}
