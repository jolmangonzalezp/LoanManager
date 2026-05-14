import { Http } from '@/Infrastructure';
import { LoanTypeDTO } from '@/Modules/Loan';

const BASE = '/loan-types'

export const LoanTypeApi = {
  async getAll(): Promise<LoanTypeDTO[]> {
    return Http.get<LoanTypeDTO[]>(BASE)
  },

  async create(name: string): Promise<LoanTypeDTO> {
    return Http.post<LoanTypeDTO>(BASE, { name })
  },
}
