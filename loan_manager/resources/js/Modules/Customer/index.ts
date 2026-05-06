export { useCustomer } from './Composable/useCustomer';
export { CustomerService } from './Service/Customer.Service';
export { CustomerMapper } from './mapper/Customer.Mapper';
export { CustomerApi } from './Api/Customer.Api';

// Types
export type {
  Customer, CustomerName, CustomerSummary, CustomerForm, LoansByCustomer
} from './Types/Customer.Type';
export type { CustomerDTO, CustomerFormDTO, CustomerNameDTO, CustomerSummaryDTO, LoansByCustomerDTO } from './Types/CustomerDTO.Type';

// Components
export { default as CustomerDetail } from './Components/CustomerDetailComponent.vue';
export { default as CustomerForms } from './Components/CustomerFormComponent.vue';
export { default as CustomerPage } from './View/CustomersView.vue';
