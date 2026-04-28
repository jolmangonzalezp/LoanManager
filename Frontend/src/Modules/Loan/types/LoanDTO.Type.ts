export interface LoanDTO {
  id: string
  loan_number?: string
  customer: {
    id: string
    name: {
      first_name: string
      middle_name: string
      last_name: string
      second_last_name: string
    }
  }
  capital: number
  remaining_debt: number
  interest_rate: number
  start_date: string
  due_date: string
  next_payment_date?: string
  status: 'active' | 'paid' | 'defaulted'
  created_at: string
  paid_capital: number
  paid_interest: number
}

export interface LoanFormDTO {
  customer: string
  capital: number
  interest_rate: number
  date_start: string
}

export interface LoanReportDTO {
  total_loans: number
  active_loans: number
  paid_loans: number
  defaulted_loans: number
  total_capital: number
  total_remaining_debt: number
  total_paid_capital: number
  total_paid_interest: number
}