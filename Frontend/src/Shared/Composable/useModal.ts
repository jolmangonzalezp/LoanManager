import { ref, computed } from 'vue'

export type ModalSize = 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'full'

type SaveCallback = (data: any) => void | Promise<void>

export interface ModalConfig {
  component?: any
  size?: ModalSize
  props?: Record<string, any>
  darkMode?: boolean
  title?: string
  onSave?: SaveCallback
  onClose?: () => void
}

const defaultModalState: ModalConfig = {
  component: null,
  size: 'md',
  props: {},
  darkMode: true,
  title: '',
  onSave: undefined,
  onClose: undefined
}

const modal = ref<ModalConfig>({ ...defaultModalState })

export function useModal() {

  const isOpen = computed(() => !!modal.value.component)
  const currentComponent = computed(() => modal.value.component)
  const currentProps = computed(() => modal.value.props || {})
  const size = computed(() => modal.value.size || 'md')
  const darkMode = computed(() => modal.value.darkMode ?? true)
  const title = computed(() => modal.value.title || '')
  const onSave = computed(() => modal.value.onSave)
  const onClose = computed(() => modal.value.onClose)

  function open(component: any, config: Partial<ModalConfig> = {}) {
    modal.value = {
      ...defaultModalState,
      ...config,
      component,
      size: config.size || 'md',
      props: config.props || {},
      darkMode: config.darkMode ?? true
    }
  }

  function close() {
    modal.value.onClose?.()
    modal.value = { ...defaultModalState }
  }

  function handleSave(data: any) {
    const callback = modal.value.onSave
    if (callback) {
      Promise.resolve(callback(data)).then(() => {
        modal.value = { ...defaultModalState }
      })
    } else {
      modal.value = { ...defaultModalState }
    }
  }

  function updateProps(props: Record<string, any>) {
    modal.value.props = { ...modal.value.props, ...props }
  }

  return {
    modal,
    isOpen,
    currentComponent,
    currentProps,
    size,
    darkMode,
    title,
    onSave,
    onClose,
    open,
    close,
    handleSave,
    updateProps
  }
}