export { CustomerApi } from './Api/Customer.Api'
export { CustomerService } from './Service/Customer.Service'
export { CustomerMapper } from './mapper/Customer.Mapper'
export { useCustomer } from './Composable/useCustomer'
export { useCustomer as useCustomerView } from './Composable/useCustomer'
export { useCustomer as useCustomerApi } from './Composable/useCustomer'


export type { 
  Customer,
  CustomerName, 
  CustomerSummary,
} from './types/Customer.Type'