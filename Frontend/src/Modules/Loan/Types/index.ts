export interface Loan {
  id: string
  customer_id: string
  customer_name?: string
  capital: { amount: number; currency: string }
  remaining_debt: { amount: number; currency: string }
  original_capital?: { amount: number; currency: string }
  paid_capital?: { amount: number; currency: string }
  paid_interest?: { amount: number; currency: string }
  interest_rate: { annual: number; monthly: number }
  start_date: string
  due_date: string
  next_payment_date?: string
  term: number
  status: LoanStatus
  created_at: string
}

export type LoanStatus = 'active' | 'pending' | 'paid' | 'defaulted' | 'cancelled'

export interface LoanReport {
  total_loans: number
  active_loans: number
  paid_loans: number
  defaulted_loans: number
  total_capital: number
  total_remaining_debt: number
  total_paid_capital: number
  total_paid_interest: number
}

export interface CreateLoanCommand {
  customer_id: string
  capital: number
  interest_rate: number
  term: number
  start_date: string
  due_date?: string
}