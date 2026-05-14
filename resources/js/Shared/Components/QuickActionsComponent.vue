<script setup lang="ts">
import {computed, ref} from "vue";
import {useRoute} from "vue-router";
import {faUserPlus, faCalendarPlus, faPlus, faUserGear, faRoute} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

import { CustomerForms } from '@/Modules/Customer';
import { RouteForms } from '@/Modules/Route';
import { UserForms } from '@/Modules/User';
import { Btn, useModal } from '@/Shared';
import { LoanForms } from '@/Modules/Loan';
import { useAuth } from '@/Modules/Auth';

const route = useRoute()
const { can, hasRole } = useAuth()
const isUserPage = computed(() => route.path === '/usuarios')
const isRoutePage = computed(() => route.path === '/rutas')

const showCreate = computed(() => {
  if (isUserPage.value) return hasRole('admin')
  if (isRoutePage.value) return true
  return can('customers.create') || can('loans.create')
})

const {open} = useModal()

const openMenu = ref(false)
const toggle = () => {
  openMenu.value = !openMenu.value
}

const openForm = (form: string) => {
  if (form === 'customer') {
    open(
        CustomerForms,
        {
          size: 'lg'
        }
    );
  }
  if (form === 'loan') {
    open(
        LoanForms,
        {
          size: 'lg'
        }
    )
  }
  if (form === 'user') {
    open(
        UserForms,
        {
          size: 'lg',
          props: { isEditing: false, id: '' }
        }
    )
  }
  if (form === 'route') {
    open(
        RouteForms,
        {
          size: 'lg',
        }
    )
  }
}

</script>

<template>
  <div v-if="showCreate" class="quick-actions">
    <Btn variant="primary" circle @click="toggle">
      <FontAwesomeIcon :icon="faPlus" />
    </Btn>
    <transition name="fab-menu">
      <div v-if="openMenu" class="quick-actions__menu">
        <template v-if="isUserPage">
          <span v-if="hasRole('admin')" class="quick-actions__item-menu" @click="openForm('user')">
            <FontAwesomeIcon :icon="faUserGear"></FontAwesomeIcon>
            Nuevo Usuario
          </span>
        </template>
        <template v-else-if="isRoutePage">
          <span class="quick-actions__item-menu" @click="openForm('route')">
            <FontAwesomeIcon :icon="faRoute"></FontAwesomeIcon>
            Nueva Ruta
          </span>
        </template>
        <template v-else>
          <span v-if="can('loans.create')" class="quick-actions__item-menu" @click="openForm('loan')">
            <FontAwesomeIcon :icon="faCalendarPlus"></FontAwesomeIcon>
            Nuevo Prestamo
          </span>
          <span v-if="can('customers.create')" class="quick-actions__item-menu" @click="openForm('customer')">
            <FontAwesomeIcon :icon="faUserPlus"></FontAwesomeIcon>
            Nuevo Cliente
          </span>
        </template>
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
