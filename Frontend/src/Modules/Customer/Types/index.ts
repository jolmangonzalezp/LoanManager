export interface Customer {
  id: string
  name: {
    first_name: string
    last_name: string
    second_last_name?: string
  }
  dni: {
    type: string
    number: string
  }
  email?: string
  phone?: string
  address?: {
    street: string
    city?: string
    department?: string
    country?: string
  }
  created_at: string
  enabled?: boolean
}

export interface CustomerSummary {
  total_customers: number
  customers_with_loans: number
  customers_without_loans: number
}

export interface CreateCustomerCommand {
  name: {
    first_name: string
    last_name: string
    second_last_name?: string
  }
  dni: {
    type: string
    number: string
  }
  email?: string
  phone?: string
  address?: {
    street: string
    city?: string
    country?: string
  }
}