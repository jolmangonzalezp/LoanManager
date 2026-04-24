export interface Payment {
    id: string
    loan: {
      id: string
      loanNumber: string
      remainingDebt: number
    }
    loanNumber: string
    amount: number
    paymentDate: string
    status: 'pending' | 'applied' | 'failed'
    interestPaid?: number
    capitalPaid?: number
    createdAt: string
}

export interface PaymentForm {
    loanId: string
    amount: number
    paymentDate: string
}

export interface PaymentReport {
    month: string
    year: number
    capitalReturned: number
    interestCollected: number
    paymentsCount: number
}

export interface PaymentByLoan {
  loanId: string
  capitalPaid: number
  interestPaid: number
  totalPaid: number
}
