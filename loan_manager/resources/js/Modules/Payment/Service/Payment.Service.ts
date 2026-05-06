import {
    Payment,
    PaymentApi,
    PaymentDTO,
    PaymentForm,
    PaymentFormDTO,
    PaymentMapper,
    PaymentReport,
    PaymentReportDTO
} from '@/Modules/Payment';

export const PaymentService = {
  async getAll(): Promise<Payment[]> {
    const response: PaymentDTO[] = await PaymentApi.getAll()
    return PaymentMapper.toDomainList(response)

  },

  async getById(id: string): Promise<Payment> {
    const response:PaymentDTO = await PaymentApi.getById(id);
    return PaymentMapper.toDomain(response);
  },

  async getMonthlyReport(): Promise<PaymentReport> {
    const response:PaymentReportDTO =  await PaymentApi.getMonthlyReport();
    return PaymentMapper.toPaymentReport(response);
  },

  async create(data: PaymentForm): Promise<string> {
    const mapped: PaymentFormDTO = PaymentMapper.toFormDTO(data)
    return PaymentApi.create(mapped)
  }
}
