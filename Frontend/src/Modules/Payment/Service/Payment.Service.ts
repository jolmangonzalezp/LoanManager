import { PaymentApi } from '../Api/Payment.Api'
import { PaymentMapper } from '../mapper/Payment.Mapper'
import type {Payment, PaymentForm, PaymentFormData, PaymentReport} from '../types/Payment.Type'
import {PaymentDTO, PaymentFormDTO} from "../types/PaymentDTO.Type";

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
    return PaymentApi.getMonthlyReport()
  },

  async create(data: PaymentFormData): Promise<string> {
    const mapped: PaymentFormDTO = PaymentMapper.toFormDTO(data)
    return PaymentApi.create(mapped)
  }
}