import Swal from 'sweetalert2'

export function useAlert() {
  function showLoading(title = 'Procesando...') {
    Swal.fire({
      title,
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    })
  }

  function close() {
    Swal.close()
  }

  function showError(message = 'Error') {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: message,
      confirmButtonColor: '#d4af37'
    })
  }

  function showSuccess(message = 'Éxito') {
    Swal.fire({
      icon: 'success',
      title: 'Éxito',
      text: message,
      confirmButtonColor: '#d4af37',
      timer: 2000
    })
  }

  function showWarning(message = 'Advertencia') {
    Swal.fire({
      icon: 'warning',
      title: 'Advertencia',
      text: message,
      confirmButtonColor: '#d4af37'
    })
  }

  async function confirm(title = '¿Estás seguro?', text = '') {
    const result = await Swal.fire({
      title,
      text,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#d4af37',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí',
      cancelButtonText: 'Cancelar'
    })
    return result.isConfirmed
  }

  return {
    showLoading,
    close,
    showError,
    showSuccess,
    showWarning,
    confirm
  }
}