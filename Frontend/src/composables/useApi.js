import { ref } from 'vue'

const API_URL = 'http://localhost:8000/api'
const token = ref(localStorage.getItem('token'))

export function useApi() {
  const loading = ref(false)
  const error = ref(null)

  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }

  if (token.value) {
    headers['Authorization'] = `Bearer ${token.value}`
  }

  async function request(endpoint, options = {}) {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_URL}${endpoint}`, {
        ...options,
        headers: { ...headers, ...options.headers }
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.error?.message || data.error?.code || 'Request failed')
      }

      return data
    } catch (e) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  function setToken(t) {
    token.value = t
    if (t) {
      localStorage.setItem('token', t)
    } else {
      localStorage.removeItem('token')
    }
  }

  return {
    loading,
    error,
    setToken,
    get: (endpoint) => request(endpoint),
    post: (endpoint, data) => request(endpoint, { method: 'POST', body: JSON.stringify(data) }),
    put: (endpoint, data) => request(endpoint, { method: 'PUT', body: JSON.stringify(data) }),
    delete: (endpoint) => request(endpoint, { method: 'DELETE' })
  }
}

export function formatCurrency(value) {
  if (value == null) return '$0'
  return '$' + Number(value).toLocaleString('es-CO')
}

export function formatDate(date) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('es-CO', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

export function getStatusLabel(status) {
  const labels = {
    active: 'Al día',
    pending: 'Pendiente',
    overdue: 'En mora',
    closed: 'Cerrado',
    paid: 'Pagado',
    defaulted: 'Mora'
  }
  return labels[status] || status
}

export function getStatusColor(status) {
  const colors = {
    active: 'success',
    pending: 'pending',
    overdue: 'overdue',
    closed: 'closed',
    paid: 'success',
    defaulted: 'overdue'
  }
  return colors[status] || 'active'
}