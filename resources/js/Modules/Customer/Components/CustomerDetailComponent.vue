<script setup lang="ts">
import {computed} from "vue";

import { useAuth } from '@/Modules/Auth';
import { CustomerForms, CustomerDocuments, useCustomer } from '@/Modules/Customer';
import { Ava, Btn, GCard, CardTitle, useModal, formatCurrency } from '@/Shared';
import { LoanDetail, LoanForms, useLoan } from '@/Modules/Loan';
import MapboxMap from '@/Modules/Geo/Components/MapboxMap.vue';

const { can } = useAuth()


const { customer, loans, fillCustomer } = useCustomer();
const { getById } = useLoan();
const {open, close} = useModal();

const hasCoordinates = computed(() =>
  customer.value?.latitude != null && customer.value?.longitude != null
)

const initials = computed(() => {
  if (!customer.value) return "??"
  const parts = [
    customer.value.name.firstName?.[0] || '',
    customer.value.name.lastName?.[0] || ''
  ].filter(Boolean)
  return parts.slice(0,2).join('').toUpperCase() || '??'
})

const updateCustomer = () => {
    if (!customer.value) return
  fillCustomer();
  close();
  open(
      CustomerForms,
      {
        size: "lg",
        props: {
          id: customer.value.id,
          isEditing: true,
        }
      }
  );
}

const newLoanHandler = () => {
    if (!customer.value) return
  close()
  open(
      LoanForms,
      {
        size: "lg",
        props: {
          customerPreselected: customer.value.id,
          disable: true
        }
      }
  );
}

const handleRowClick = async (id: string) => {
  await getById(id);
  close()
  open(
      LoanDetail,
      {
        size: "lg",
      }
  );
}

</script>

<template>
  <div v-if="customer" class="custumer-details">
    <section class="custumer-details__header">
      <Ava :initials="initials" :size="52" />
      <div class="info">
        <div class="name">
          {{ customer.name.firstName }}
          {{ customer.name.middleName }}
          {{ customer.name.lastName }}
          {{ customer.name.secondLastName }}
        </div>
        <div class="meta">
          {{ customer?.dni?.type }}.: {{ customer?.dni?.number }} · Registro:
          {{ customer?.createdAt || '—' }}
        </div>
      </div>
    </section>
    <section class="custumer-details__content">
      <GCard class="custumer-details__card">
        <CardTitle>Informacion Personal</CardTitle>
        <div class="info-row">
          <label>Telefono</label>
          <div>{{ customer?.phone || '—' }}</div>
        </div>
        <div class="info-row">
          <label>Email</label>
          <div>{{ customer?.email || "" || '—' }}</div>
        </div>
        <div class="info-row">
          <label>Dirección</label>
          <div>{{ customer?.address || '—' }}</div>
        </div>
      </GCard>

      <GCard v-if="hasCoordinates" class="custumer-details__card">
        <CardTitle>Ubicación</CardTitle>
        <div class="custumer-details__map">
          <MapboxMap
            :center="[customer.longitude!, customer.latitude!]"
            :zoom="14"
            :marker="[customer.longitude!, customer.latitude!]"
          />
        </div>
      </GCard>

      <GCard class="custumer-details__card" v-if="loans">
        <CardTitle>Préstamos</CardTitle>
        <div class="loan-list">
          <div class="loan-item" v-for="loan in loans" :key="loan.id" :class="loan.status" @click="handleRowClick(loan.id)">
            <span class="loan-id">{{ loan.loanNumber}}</span>
            <span class="loan-amount">{{ formatCurrency(loan.balance) }}</span>
          </div>
        </div>
      </GCard>

      <CustomerDocuments :customer-id="customer.id" />
    </section>
    <div class="btns">
      <Btn v-if="can('customers.update')" variant="secondary" @click="updateCustomer">Actualizar</Btn>
      <Btn v-if="can('loans.create')" @click="newLoanHandler">Nuevo Prestamo</Btn>
    </div>
  </div>
</template>

<style scoped>
.custumer-details {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.custumer-details__header {
  display: flex;
  align-items: center;
  gap: 16px;
}

.info {
  flex: 1;
}

.name {
  font-size: 18px;
  font-weight: 300;
}

.meta {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 4px;
}

.custumer-details__card{
  margin-top: 1rem;
}

.custumer-details__map {
  width: 100%;
  height: 300px;
  border-radius: 8px;
  overflow: hidden;
  margin-top: 8px;
}

.info-row {
  display: flex;
  justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
  padding: 10px 0;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.info-row label {
  font-size: 10px;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
    width: 100px;
}

.info-row div {
    max-width: 300px;
}

.info-row span:first-child {
  color: #94a3b8;
  font-size: 12px;
}

.btns{
  width: 100%;
  display: flex;
  justify-content: space-between;
}

.loan-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 10rem;
  overflow-y: auto ;
}

.loan-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s;
}

.loan-item.active {
  background: #0a1f1a;
}

.loan-item.defaulted {
  background: #dc2626;
}

.loan-item.paid {
  background: rgba(0,0,0,0.2);;
}

.loan-item:hover {
  background: rgba(212,175,55,0.08);
}

.loan-info {
  flex: 1;
}

.loan-id {
  /* font-family: 'Share Tech Mono', monospace; */
  font-size: 14px;
}

.loan-amount {
  /* font-family: 'Share Tech Mono', monospace; */
  font-weight: 700;
  color: #fff;
  font-size: 14px;
}

.actions {
  display: flex;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
}

.loading {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}
</style>
