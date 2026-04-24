import { ref, computed, watch } from 'vue'

export function usePagination<T>(
  dataSource: () => T[],
  itemsByPage: number = 10
) {
  const currentPage = ref(1)
  
  const data = ref<T[]>([])
  
  function setData(newData: T[]) {
    data.value = newData
    currentPage.value = 1
  }

  const totalItems = computed(() => data.value.length)
  
  const totalPages = computed(() => 
    Math.ceil(totalItems.value / itemsByPage) || 1
  )
  
  const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * itemsByPage
    const end = start + itemsByPage
    return data.value.slice(start, end)
  })

  const hasNextPage = computed(() => currentPage.value < totalPages.value)
  const hasPrevPage = computed(() => currentPage.value > 1)
  const isFirstPage = computed(() => currentPage.value === 1)
  const isLastPage = computed(() => currentPage.value === totalPages.value)

  function setPage(page: number) {
    if (page >= 1 && page <= totalPages.value) {
      currentPage.value = page
    }
  }

  function nextPage() {
    if (hasNextPage.value) {
      currentPage.value++
    }
  }

  function prevPage() {
    if (hasPrevPage.value) {
      currentPage.value--
    }
  }

  function firstPage() {
    currentPage.value = 1
  }

  function lastPage() {
    currentPage.value = totalPages.value
  }

  function reset() {
    currentPage.value = 1
  }

  return {
    data,
    setData,
    currentPage,
    totalItems,
    totalPages,
    paginatedData,
    hasNextPage,
    hasPrevPage,
    isFirstPage,
    isLastPage,
    setPage,
    nextPage,
    prevPage,
    firstPage,
    lastPage,
    reset
  }
}