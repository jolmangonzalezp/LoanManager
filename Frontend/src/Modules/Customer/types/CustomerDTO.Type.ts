export interface CustomerDTO {
    id: string;
    name: {
        first_name: string;
        middle_name?: string | null;
        last_name: string;
        second_last_name: string;
    },
    full_name: string;
    partial_name: string;
    dni: {
        type: string;
        number: string;
    };
    full_dni: string;
    email?: string;
    phone: string;
    address: string;
    enabled?: boolean;
    created_at?: string;
}

export interface CustomerFormDTO {
    name: {
        first_name: string;
        middle_name: string | null;
        last_name: string;
        second_last_name: string;
    }
    dni: {
        type: string
        number: string
    }
    phone: string
    address: string
    email?: string
}

export interface CustomerNameDTO {
    id: string
    name:{
        first_name: string;
        middle_name: string | null;
        last_name: string;
        second_last_name: string | null;
    }
}

export interface CustomerSummaryDTO {
    total_customers: number
    customers_with_loans: number
    customers_without_loans: number
}

export interface LoansByCustomerDTO {
    id: string;
    loan_number: string;
    balance: number;
    status: string;
    due_date: string;
}