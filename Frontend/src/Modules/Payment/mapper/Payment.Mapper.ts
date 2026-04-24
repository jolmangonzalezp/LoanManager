import type { Payment, PaymentForm, PaymentReport, PaymentByLoan } from '../types/Payment.Type'
import type { PaymentDTO, PaymentFormDTO, PaymentReportDTO, PaymentByLoanDTO } from '../types/PaymentDTO.Type'

export const PaymentMapper = {
    toDomain(dto: PaymentDTO): Payment {
        return {
            id: dto.id,
            loan: {
                id: dto.loan.id,
                loanNumber: dto.loan.loan_number || '',
                remainingDebt: dto.loan.remaining_debt
            },
            loanNumber: dto.loan.loan_number || '',
            amount: dto.amount,
            paymentDate: dto.payment_date,
            status: dto.status,
            interestPaid: dto.interest_paid,
            capitalPaid: dto.capital_paid,
            createdAt: dto.created_at
        }
    },

    toFormDTO(form: PaymentForm): PaymentFormDTO {
        return {
            loan_id: form.loanId,
            amount: form.amount,
            payment_date: form.paymentDate
        }
    },

    toPaymentReport(dto: PaymentReportDTO): PaymentReport {
        return {
            month: dto.month,
            year: dto.year,
            capitalReturned: dto.capital_returned,
            interestCollected: dto.interest_collected,
            paymentsCount: dto.payments_count
        }
    },

    toPaymentByLoan(dto: PaymentByLoanDTO): PaymentByLoan {
        return {
            loanId: dto.loan_id,
            capitalPaid: dto.capital_paid,
            interestPaid: dto.interest_paid,
            totalPaid: dto.total_paid
        }
    },

    toDomainList(dtos: PaymentDTO[]): Payment[] {
        return dtos.map(dto => this.toDomain(dto))
    }
}