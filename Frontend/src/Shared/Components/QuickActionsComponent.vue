<script setup lang="ts">
import {ref} from "vue";
import {Btn, useModal} from "@Shared";
import {faUserPlus, faCalendarPlus, faPlus} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import CustomerFormComponent from "@Modules/Customer/Component/CustomerFormComponent.vue";
import LoanFormComponent from "@Modules/Loan/Component/LoanFormComponent.vue";

const {open, close} = useModal()

const openMenu = ref(false)
const toggle = () => {
  openMenu.value = !openMenu.value
}

const openForm = (form: string) => {
  if (form === 'customer') {
    open(
        CustomerFormComponent,
        {
          size: 'lg'
        }
    );
  }
  if (form === 'loan') {
    open(
        LoanFormComponent,
        {
          size: 'lg'
        }
    )
  }
}

</script>

<template>
  <div class="quick-actions">
    <Btn variant="primary" circle @click="toggle">
      <FontAwesomeIcon :icon="faPlus" />
    </Btn>
    <transition name="fab-menu">
      <div v-if="openMenu" class="quick-actions__menu">
        <span class="quick-actions__item-menu" @click="openForm('loan')">
          <FontAwesomeIcon :icon="faCalendarPlus"></FontAwesomeIcon>
          Nuevo Prestamo
        </span>
        <span class="quick-actions__item-menu" @click="openForm('customer')">
          <FontAwesomeIcon :icon="faUserPlus"></FontAwesomeIcon>
          Nuevo Cliente
        </span>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.quick-actions{
  position: fixed;
  bottom: 60px;
  right: 40px;
  display: flex;
  flex-direction: column-reverse;
  align-items: flex-end;
  gap: 5px;
  z-index: 500;
}

.quick-actions__menu{
  display: flex;
  flex-direction: column;
  background: rgba(212,175,55,0.1);
  border: 1px solid rgba(212,175,55,0.28);
  backdrop-filter: blur(8px);
  border-radius: 10px;
  color: #d4af37;
}

.quick-actions__item-menu{
  cursor: pointer;
  font-size: 1rem;
  font-weight: 700;
  margin: 0.5rem;
}

</style>