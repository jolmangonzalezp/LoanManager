import { ref } from 'vue'

export function useDataLoader(apiMethod, onLoad = null) {
  const loading = ref(false)
  const error = ref(null)
  const data = ref([])

  async function load() {
    loading.value = true
    error.value = null
    try {
      const result = await apiMethod()
      data.value = result || []
      if (onLoad) onLoad(result)
      return result
    } catch (e) {
      console.error('useDataLoader error:', e)
      error.value = e.message || 'Error al cargar datos'
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    data,
    load
  }
}

export function useModalState() {
  const showForm = ref(false)
  const showDetail = ref(false)
  const showPayment = ref(false)
  const editing = ref(null)
  const selected = ref(null)

  function openForm(item = null) {
    editing.value = item
    showForm.value = true
  }

  function closeForm() {
    showForm.value = false
    editing.value = null
  }

  function openDetail(item) {
    selected.value = item
    showDetail.value = true
  }

  function closeDetail() {
    showDetail.value = false
    selected.value = null
  }

  function closeAll() {
    closeForm()
    closeDetail()
    showPayment.value = false
  }

  return {
    showForm,
    showDetail,
    showPayment,
    editing,
    selected,
    openForm,
    closeForm,
    openDetail,
    closeDetail,
    closeAll
  }
}