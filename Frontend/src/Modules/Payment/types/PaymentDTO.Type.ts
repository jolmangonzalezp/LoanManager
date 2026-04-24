export interface PaymentDTO {
    id: string
    loan: {
        id: string
        loan_number: string
        remaining_debt: number
    }
    loan_number?: string
    amount: number
    payment_date: string
    status: 'pending' | 'applied' | 'failed'
    interest_paid?: number
    capital_paid?: number
    created_at: string
}

export interface PaymentFormDTO {
    loan_id: string
    amount: number
    payment_date: string
}

export interface PaymentReportDTO {
    month: string
    year: number
    capital_returned: number
    interest_collected: number
    payments_count: number
}

export interface PaymentByLoanDTO {
    loan_id: string
    capital_paid: number
    interest_paid: number
    total_paid: number
}