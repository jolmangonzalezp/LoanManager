import {CustomerKPI} from "../types/Report.Type";
import {CustomerKPIDTO} from "../types/reportDTO.Type";

export const ReportMapper = {
    toCustomerKPI(dto: CustomerKPIDTO): CustomerKPI {
        return {
            totalCustomers: dto.total_customers,
            customersWithActiveLoans: dto.customers_with_active_loans,
            customersWithLoans: dto.customers_with_loans,
            customersWithoutLoans: dto.customers_without_loans,
            activeCustomers: dto.active_customers,
        }
    }
}