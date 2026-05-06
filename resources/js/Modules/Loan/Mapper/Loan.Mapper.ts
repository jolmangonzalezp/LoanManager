import { Loan, LoanDTO, LoanForm, LoanFormDTO, LoanReport, LoanReportDTO } from '@/Modules/Loan';

export const LoanMapper = {
  toDomain(dto: LoanDTO): Loan {
    return {
      id: dto.id,
      loanNumber: dto.loan_number || "",
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
