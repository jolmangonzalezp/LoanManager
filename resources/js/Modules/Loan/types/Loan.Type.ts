export interface Loan {
  id: string
  loanNumber: string
  customer: {
    id: string
    name: {
      firstName: string
      middleName?: string
      lastName: string
      secondLastName: string
    }
  }
  partialName: string
  fullName: string
  capital: number | string
  remainingDebt: number
  interestRate: number
  startDate: string
  dueDate: string
  nextPaymentDate?: string
  status: 'active' | 'paid' | 'defaulted'
  createdAt: string
  progress?: number
  paidCapital: number
  paidInterest: number
  loanTypeId?: string
  loanType?: string
}

export interface LoanForm {
  customer: string
  capital: number
  interestRate: number
  dateStart: string
  loanTypeId?: string
  loanTypeName?: string
}

export interface LoanReport {
  totalLoans: number
  activeLoans: number
  paidLoans: number
  defaultedLoans: number
  totalCapital: number
  totalRemainingDebt: number
  totalPaidCapital: number
  totalPaidInterest: number
}
