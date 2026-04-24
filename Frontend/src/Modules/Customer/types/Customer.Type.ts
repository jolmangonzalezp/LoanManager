export interface Customer {
  id: string
  name: {
    firstName: string
    middleName?: string
    lastName: string
    secondLastName: string
  }
  fullName: string
  partialName: string
  dni: {
    type: string
    number: string
  }
  fullDni: string
  phone: string
  address: string
  email?: string
  createdAt: string
  enabled: boolean
}

export interface CustomerForm {
  name: {
    firstName: string
    middleName?: string
    lastName: string
    secondLastName: string
  }
  dni: {
    type: string
    number: string
  }
  phone: string
  address: string
  email?: string
}

export interface CustomerName {
  id: string
  name: {
    firstName: string
    middleName?: string
    lastName: string
    secondLastName: string
  }
}

export interface CustomerSummary {
  totalCustomers: number
  customersWithLoans: number
  customersWithoutLoans: number
}

export interface LoansByCustomer {
  id: string;
  loanNumber: string;
  balance: number;
  status: string;
  dueDate: string;
}

export type CustomerStatus = 'active' | 'inactive'
