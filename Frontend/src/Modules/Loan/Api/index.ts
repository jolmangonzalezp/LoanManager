import { useApi } from '@/Shared/Composable/useApi'
import { useCrud } from '@/Shared/Composable/useCrud'

export function useLoanApi() {
  const api = useApi()

  async function getAll() {
    return api.get('/loans')
  }

  async function getById(id) {
    return api.get(`/loans/${id}`)
  }

  async function getReport() {
    return api.get('/loans/report')
  }

  async function create(data) {
    return api.post('/loans', data)
  }

  async function update(id, data) {
    return api.put(`/loans/${id}`, data)
  }

  async function remove(id) {
    return api.delete(`/loans/${id}`)
  }

  async function processPayment(loanId, amount, paymentDate) {
    return api.post('/payments', {
      loan_id: loanId,
      amount,
      payment_date: paymentDate || new Date().toISOString().split('T')[0]
    })
  }

  const crud = useCrud({ create, update, remove })

  return {
    loading: api.loading,
    error: api.error,
    getAll,
    getById,
    getReport,
    create: (data) => crud.create(data, 'Préstamo creado'),
    update: (id, data) => crud.update(id, data, 'Préstamo actualizado'),
    delete: (id) => crud.remove(id, 'Préstamo eliminado'),
    processPayment
  }
}