import type { Loan, LoanModelView, LoanForm, LoanReport } from '../types/Loan.Type'
import type { LoanDTO, LoanFormDTO, LoanReportDTO } from '../types/LoanDTO.Type'

export const LoanMapper = {
  toDomain(dto: LoanDTO): Loan {
    return {
      id: dto.id,
      loanNumber: dto.loan_number,
      customer: {
        id: dto.customer.id,
        name: {
          firstName: dto.customer.name.first_name,
          middleName: dto.customer.name.middle_name,
          lastName: dto.customer.name.last_name,
          secondLastName: dto.customer.name.second_last_name,
        },
      },
      partialName: dto.customer.name.first_name + " " + dto.customer.name.last_name,
      fullName: dto.customer.name.first_name + " " + dto.customer.name.middle_name + " " + dto.customer.name.last_name + " " + dto.customer.name.second_last_name,
      capital: dto.capital,
      remainingDebt: dto.remaining_debt,
      interestRate: dto.interest_rate,
      startDate: dto.start_date,
      dueDate: dto.due_date,
      nextPaymentDate: dto.next_payment_date,
      status: dto.status,
      createdAt: dto.created_at,
      progress: ((dto.capital - dto.remaining_debt) / dto.capital) * 100,
      paidCapital: dto.paid_capital,
      paidInterest: dto.paid_interest
    }
  },

  toModelView(dto: LoanDTO): LoanModelView {
    const capital = dto.capital || 0
    const paid = dto.capital - dto.remaining_debt
    const progress = capital > 0 ? Math.round((paid / capital) * 100) : 0

    const fullName = [
      dto.customer.name.first_name,
      dto.customer.name.middle_name,
      dto.customer.name.last_name,
      dto.customer.name.second_last_name,
    ].filter(Boolean).join(' ')

    return {
      id: dto.id,
      loan_number: dto.loan_number || dto.id.slice(0, 8),
      customer_id: dto.customer.id,
      customer_name: fullName,
      capital: dto.capital,
      remaining_debt: dto.remaining_debt,
      interest_rate: dto.interest_rate,
      status: dto.status,
      start_date: dto.start_date,
      due_date: dto.due_date,
      progress,
    }
  },

  toFormDTO(form: LoanForm): LoanFormDTO {
    return {
      customer: form.customer,
      capital: form.capital,
      interest_rate: form.interestRate,
      date_start: form.dateStart
    }
  },

  toReport(dto: LoanReportDTO): LoanReport {
    return {
      totalLoans: dto.total_loans,
      activeLoans: dto.active_loans,
      paidLoans: dto.paid_loans,
      defaultedLoans: dto.defaulted_loans,
      totalCapital: dto.total_capital,
      totalRemainingDebt: dto.total_remaining_debt,
      totalPaidCapital: dto.total_paid_capital,
      totalPaidInterest: dto.total_paid_interest,
    }
  },

  toDomainInList(dtos: LoanDTO[]): Loan[] {
    return dtos.map(dto => this.toDomain(dto))
  }
}