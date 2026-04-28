import { LoanApi } from '../Api/Loan.Api'
import { LoanMapper } from '../mapper/Loan.Mapper'
import type { Loan, LoanModelView, LoanForm, LoanReport } from '../types/Loan.Type'
import { LoanDTO, LoanFormDTO, LoanReportDTO } from '../types/LoanDTO.Type'

export const LoanService = {
  async getAll(): Promise<Loan[]> {
    const response: LoanDTO[] = await LoanApi.getAll();
    return LoanMapper.toDomainInList(response);
  },

  async getById(id: string): Promise<Loan> {
    const response: LoanDTO = await LoanApi.getById(id);
    return LoanMapper.toDomain(response);
  },

  async getReport(): Promise<LoanReport> {
    const response: LoanReportDTO = await LoanApi.getReport()
    return LoanMapper.toReport(response)
  },

  async create(data: LoanForm): Promise<string> {

    const mapped: LoanFormDTO = LoanMapper.toFormDTO(data)
    return await LoanApi.create(mapped)
  },

  async update(id: string, data: LoanForm): Promise<string> {
    const mapped = LoanMapper.toFormDTO(data)
    return LoanApi.update(id, mapped)
  },

  async delete(id: string): Promise<void> {
    return LoanApi.delete(id)
  },

  async processPayment(id: string, amount: number, payment_date: string): Promise<Loan> {
    const response: LoanDTO = await LoanApi.processPayment(id, amount, payment_date)
    return LoanMapper.toDomain(response)
  }
}