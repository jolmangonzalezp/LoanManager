import { ref } from 'vue'

const showNewLoan = ref(false)
const showNewCustomer = ref(false)
const showNewPayment = ref(false)

export function useGlobalModals() {
  function openNewLoan() {
    showNewLoan.value = true
  }
  function openNewCustomer() {
    showNewCustomer.value = true
  }
  function openNewPayment() {
    showNewPayment.value = true
  }
  function closeAll() {
    showNewLoan.value = false
    showNewCustomer.value = false
    showNewPayment.value = false
  }

  return {
    showNewLoan,
    showNewCustomer,
    showNewPayment,
    openNewLoan,
    openNewCustomer,
    openNewPayment,
    closeAll
  }
}