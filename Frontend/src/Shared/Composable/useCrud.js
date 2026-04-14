import { ref } from 'vue'

export function useCrud(apiMethod) {
  const loading = ref(false)
  const error = ref(null)

  async function create(data, successMsg = 'Creado exitosamente') {
    loading.value = true
    error.value = null
    try {
      const result = await apiMethod.create(data)
      return result
    } catch (e) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  async function update(id, data, successMsg = 'Actualizado exitosamente') {
    loading.value = true
    error.value = null
    try {
      const result = await apiMethod.update(id, data)
      return result
    } catch (e) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  async function remove(id, successMsg = 'Eliminado exitosamente') {
    loading.value = true
    error.value = null
    try {
      const result = await apiMethod.delete(id)
      return result
    } catch (e) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    create,
    update,
    remove
  }
}