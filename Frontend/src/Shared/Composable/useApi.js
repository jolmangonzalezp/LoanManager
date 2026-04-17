import { ref } from 'vue'

const API_URL = 'http://localhost:8000/api'

let token = ref(localStorage.getItem('token'))

export function useApi() {
  const loading = ref(false)
  const error = ref(null)

  const getHeaders = () => {
    const h = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }
    if (token.value) {
      h['Authorization'] = `Bearer ${token.value}`
    }
    return h
  }

  async function request(endpoint, options = {}) {
    loading.value = true
    error.value = null

    console.log('API Request:', options.method || 'GET', `${API_URL}${endpoint}`)
    console.log('Headers:', getHeaders())
    console.log('Body:', options.body)

    try {
      const response = await fetch(`${API_URL}${endpoint}`, {
        ...options,
        headers: { ...getHeaders(), ...options.headers }
      })

      console.log('Response status:', response.status)
      const data = await response.json()
      console.log('Response data:', data)

      if (!response.ok) {
        throw new Error(data.error?.message || data.error?.code || 'Request failed')
      }

      return data
    } catch (e) {
      console.error('API Error:', e)
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

  function initToken() {
    token.value = localStorage.getItem('token')
  }

  return {
    loading,
    error,
    setToken,
    initToken,
    get: (endpoint) => request(endpoint),
    post: (endpoint, data) => request(endpoint, { method: 'POST', body: JSON.stringify(data) }),
    put: (endpoint, data) => request(endpoint, { method: 'PUT', body: JSON.stringify(data) }),
    delete: (endpoint) => request(endpoint, { method: 'DELETE' })
  }
}

export function formatCurrency(value) {
  if (value == null) return '$0'
  const num = Math.round(typeof value === 'number' ? value : Number(value))
  return '$' + num.toLocaleString('es-CO')
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