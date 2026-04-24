import type {Customer, CustomerForm, CustomerName, CustomerSummary, LoansByCustomer} from '../types/Customer.Type'
import {
  CustomerDTO,
  CustomerFormDTO,
  CustomerNameDTO,
  CustomerSummaryDTO,
  LoansByCustomerDTO
} from "../types/CustomerDTO.Type";

export const CustomerMapper = {

  toDomain(dto: CustomerDTO): Customer{
      return {
        id: dto.id,
        name: {
          firstName: dto.name.first_name,
          middleName: dto.name.middle_name,
          lastName: dto.name.last_name,
          secondLastName: dto.name.second_last_name,
        },
        fullName: dto.name.first_name + " " + dto.name.middle_name + " " + dto.name.last_name + " " + dto.name.second_last_name,
        partialName: dto.name.first_name + " " + dto.name.last_name,
        dni: {
          type: dto.dni.type,
          number: dto.dni.number,
        },
        fullDni: dto.dni.type + " " + dto.dni.number,
        phone: dto.phone,
        address: dto.address,
        email: dto.email || "",
        createdAt: dto.created_at || "",
        enabled: dto.enabled,
      }
  },

  toCustumerForm(dto: CustomerFormDTO): CustomerForm {
    return {
      name: {
        firstName: dto.name.first_name,
        middleName: dto.name.middle_name,
        lastName: dto.name.last_name,
        secondLastName: dto.name.second_last_name,
      },
      dni: {
        type: dto.dni.type,
        number: dto.dni.number,
      },
      phone: dto.phone,
      address: dto.address,
      email: dto.email || "",
    }
  },

  toCustomerName(dto: CustomerNameDTO):CustomerName {
    return {
      id: dto.id,
      name: {
        firstName: dto.name.first_name,
        middleName: dto.name.middle_name,
        lastName: dto.name.last_name,
        secondLastName: dto.name.second_last_name,
      }
    }
  },

  toCustomerSummary(dto: CustomerSummaryDTO):CustomerSummary {
    return {
      totalCustomers: dto.total_customers,
      customersWithLoans: dto.customers_with_loans,
      customersWithoutLoans: dto.customers_without_loans
    }
  },

  toCustomerFormDTO(domain: CustomerForm):CustomerFormDTO {
    return {
      name: {
        first_name: domain.name.firstName,
        middle_name: domain.name.middleName,
        last_name: domain.name.lastName,
        second_last_name: domain.name.secondLastName
      },
      dni: {
        type: domain.dni.type,
        number: domain.dni.number,
      },
      phone: domain.phone,
      address: domain.address,
      email: domain.email,
    }
  },

  toLoansByCustomer(dto: LoansByCustomerDTO):LoansByCustomer {
    return {
      id: dto.id,
      loanNumber: dto.loan_number,
      balance: dto.balance,
      status: dto.status,
      dueDate: dto.due_date,
    }
  },

  toDomainInList(dtos: CustomerDTO[]):Customer[] {
    return dtos.map(dtos => this.toDomain(dtos));
  },

  toLoansByCustomerInList(dto: LoansByCustomerDTO[]):LoansByCustomer[] {
    return dto.map(dtos => this.toLoansByCustomer(dtos));
  }

}