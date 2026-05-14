import { ref } from 'vue'

export function useDataLoader<T>(fetcher: () => Promise<T>) {
  const loading = ref(false)
  const data = ref<T | null>(null)

  const load = async () => {
    loading.value = true
    try {
      data.value = await fetcher()
    } finally {
      loading.value = false
    }
  }

  return { loading, data, load }
}
