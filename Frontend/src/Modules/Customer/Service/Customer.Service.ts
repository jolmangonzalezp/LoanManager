import { CustomerApi } from '../Api/Customer.Api'
import { CustomerMapper } from '../mapper/Customer.Mapper'
import type {Customer, CustomerName, CustomerForm, LoansByCustomer} from '../types/Customer.Type'
import {CustomerDTO, CustomerFormDTO, CustomerNameDTO, LoansByCustomerDTO} from "../types/CustomerDTO.Type";

export const CustomerService = {

  async getAll(): Promise<Customer[]> {
    const response: CustomerDTO[] = await CustomerApi.getAll();
    return CustomerMapper.toDomainInList(response);
  },

  async getById(id: string): Promise<Customer> {
    const response:CustomerDTO = await CustomerApi.getById(id);
    return CustomerMapper.toDomain(response);
  },

  async getSummary(): Promise<CustomerName[]> {
    const response: CustomerNameDTO[] = await CustomerApi.getSummary();
    return response.map(dto => CustomerMapper.toCustomerName(dto))
  },

  async getLoans(id: string): Promise<LoansByCustomer[]> {
    const response:LoansByCustomerDTO[] = await CustomerApi.getLoans(id);
    return CustomerMapper.toLoansByCustomerInList(response);
  },

  async create(data: CustomerForm): Promise<string> {
    const mapped: CustomerFormDTO = CustomerMapper.toCustomerFormDTO(data);
    return CustomerApi.create(mapped);
  },

  async update(id: string, data: CustomerForm): Promise<Customer> {
    const mapped: CustomerFormDTO = CustomerMapper.toCustomerFormDTO(data)
    return CustomerApi.update(id, mapped)
  },

  async delete(id: string): Promise<void> {
    return CustomerApi.delete(id)
  }
}