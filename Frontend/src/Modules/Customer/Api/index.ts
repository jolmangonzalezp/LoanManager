import { useApi } from '@/Shared/Composable/useApi'

function mapFormToBackend(data) {
  return {
    first_name: data.name?.first_name || '',
    middle_name: data.name?.last_name || null,
    last_name: data.name?.second_last_name || '',
    second_last_name: data.name?.third_last_name || '',
    dni_type: data.dni?.type || 'CC',
    dni_number: data.dni?.number || '',
    email: data.email || '',
    phone: data.phone || '',
    address: data.address?.street || ''
  }
}

export function useCustomerApi() {
  const api = useApi()

  async function getAll() {
    console.log('GET /customers')
    return api.get('/customers')
  }

  async function getById(id) {
    console.log('GET /customers/' + id)
    return api.get(`/customers/${id}`)
  }

  async function getSummary() {
    console.log('GET /customers/summary')
    return api.get('/customers/summary')
  }

  async function createCustomer(data) {
    const backendData = mapFormToBackend(data)
    console.log('POST /customers', backendData)
    return api.post('/customers', backendData)
  }

  async function updateCustomer(id, data) {
    const backendData = mapFormToBackend(data)
    console.log('PUT /customers/' + id, backendData)
    return api.put(`/customers/${id}`, backendData)
  }

  async function deleteCustomer(id) {
    console.log('DELETE /customers/' + id)
    return api.delete(`/customers/${id}`)
  }

  return {
    loading: api.loading,
    error: api.error,
    getAll,
    getById,
    getSummary,
    create: createCustomer,
    update: updateCustomer,
    remove: deleteCustomer
  }
}