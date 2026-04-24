import Swal, { type SweetAlertIcon, type SweetAlertOptions } from 'sweetalert2'

// Configuración de marca para CashMGR (colores y diseño oscuro)
const BRAND_COLORS = {
  success: '#1abc9c',
  error: '#e74c3c',
  warning: '#ffc107',
  info: '#007bff',
  bg: '#0F1B2D',
  text: '#ecf0f1'
}

const baseConfig: SweetAlertOptions = {
  background: BRAND_COLORS.bg,
  color: BRAND_COLORS.text,
  timer: 2500,
  timerProgressBar: true,
  showConfirmButton: false,
}

export function useNotifier() {

  const fire = (options: SweetAlertOptions) => {
    return Swal.fire({ ...baseConfig, ...options })
  }

  const toast = (title: string, text: string, icon: SweetAlertIcon, color: string) => {
    return Swal.fire({
      ...baseConfig,
      title,
      text,
      icon,
      toast: true,
      position: 'top',
      background: color,
      iconColor: BRAND_COLORS.text,
      color: BRAND_COLORS.text,
    })
  }

  return {
    success: (title: string, message: string) =>
        fire({ title, text: message, icon: 'success', iconColor: BRAND_COLORS.success }),

    error: (title: string, message: string) =>
        fire({ title, text: message, icon: 'error', iconColor: BRAND_COLORS.error }),

    loading: (title: string, message: string) =>
        fire({
          title,
          text: message,
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        }),


    toastSuccess: (message: string) => toast('Éxito', message, 'success', BRAND_COLORS.success),
    toastError: (message: string) => toast('Error', message, 'error', BRAND_COLORS.error),
    toastWarning: (message: string) => toast('Atención', message, 'warning', BRAND_COLORS.warning),

    close: () => Swal.close()
  }
}