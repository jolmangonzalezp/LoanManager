export interface Payment {
  id: string
  loan_id: string
  amount: number
  payment_date: string
  status: PaymentStatus
  interest_paid?: number
  capital_paid?: number
  created_at: string
  loan?: {
    id: string
    capital: { amount: number }
    customer?: {
      name: { first_name: string; last_name: string }
    }
  }
}

export type PaymentStatus = 'pending' | 'applied' | 'rejected'

export interface CreatePaymentCommand {
  loan_id: string
  amount: number
  payment_date?: string
  currency?: string
}

export interface ProjectedVsActual {
  loan_id: string
  customer_id: string
  customer_name: string
  projected_debt: number
  actual_debt: number
  difference: number
  as_of_date: string
}

export interface CollectionAvailability {
  total_capital_available: number
  currency: string
  active_loans: number
  total_loans: number
  as_of_date: string
}

export interface ClientProfitability {
  customer_id: string
  customer_name: string
  total_paid: number
  interest_paid: number
  capital_paid: number
  loan_count: number
  as_of_date: string
}