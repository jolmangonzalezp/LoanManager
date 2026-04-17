<script setup>
import { computed } from 'vue'
import Modal from '@/Shared/Components/Modal.vue'
import GCard from '@/Shared/Components/GCard.vue'
import CardTitle from '@/Shared/Components/CardTitle.vue'
import Badge from '@/Shared/Components/Badge.vue'
import Ava from '@/Shared/Components/Ava.vue'
import Btn from '@/Shared/Components/Btn.vue'
import { formatCurrency, getStatusLabel, getStatusColor } from '@/Shared/Composable/useApi'


const props = defineProps({
  open: Boolean,
  customer: Object,
  loans: Array,
  loading: Boolean
})

const emit = defineEmits(['close', 'edit', 'newLoan'])

const initials = computed(() => {
  if (!props.customer) return '??'
  const parts = [
    props.customer.first_name?.[0] || '',
    props.customer.middle_name?.[0] || '',
    props.customer.last_name?.[0] || '',
    props.customer.second_last_name?.[0] || ''
  ].filter(Boolean)
  return parts.slice(0, 2).join('').toUpperCase() || '??'
})

const fullName = computed(() => {
  if (!props.customer) return 'Cliente'
  const f = props.customer.first_name || ''
  const m = props.customer.middle_name ? props.customer.middle_name + ' ' : ''
  const l = props.customer.last_name || ''
  const s = props.customer.second_last_name || ''
  return `${f} ${m}${l} ${s}`.trim()
})

const totalDebt = computed(() => 
  props.loans?.reduce((sum, l) => sum + (l.remaining_debt || l.capital || 0), 0) || 0
)
</script>

<template>
  <Modal :open="open" :title="fullName" @close="emit('close')">
    <div v-if="loading" class="loading">Cargando...</div>
    <template v-else-if="customer">
    <div class="detail">
      <div class="header">
        <Ava :initials="initials" :size="52" />
        <div class="info">
          <div class="name">{{ fullName }}</div>
          <div class="meta">
            {{ customer?.dni?.type }}-{{ customer?.dni?.number }} · 
            {{ customer?.address || '—' }}
          </div>
        </div>
      </div>

      <GCard style="margin-top: 20px">
        <CardTitle>Información Personal</CardTitle>
        <div class="info-row">
          <label>Email</label>
          <div>{{ customer?.email || '—' }}</div>
        </div>
        <div class="info-row">
          <label>Teléfono</label>
          <div>{{ customer?.phone || '—' }}</div>
        </div>
        <div class="info-row">
          <label>Dirección</label>
          <div>{{ customer?.address || '—' }}</div>
        </div>
        <div class="info-row">
          <label>Fecha Registro</label>
          <div>{{ customer?.created_at || '—' }}</div>
        </div>
      </GCard>

      <GCard style="margin-top: 16px">
        <CardTitle>Resumen Financiero</CardTitle>
        <div class="info-row">
          <span>Deuda total activa</span>
          <span class="amount danger">{{ formatCurrency(totalDebt) }}</span>
        </div>
        <div class="info-row">
          <span>Préstamos</span>
          <span>{{ loans?.length || 0 }}</span>
        </div>
      </GCard>

      <div v-if="loans?.length" class="loans">
        <CardTitle>Préstamos</CardTitle>
        <div class="loan-list">
          <div v-for="loan in loans" :key="loan.id" class="loan-item" @click="emit('viewLoan', loan)">
            <div class="loan-info">
              <span class="loan-id">#{{ loan.id?.slice(0, 8) }}</span>
              <span class="loan-type">Personal</span>
            </div>
            <div class="loan-amount">
              {{ formatCurrency(loan.capital || loan.remaining_debt || 0) }}
            </div>
            <Badge :type="getStatusColor(loan.status)">{{ getStatusLabel(loan.status) }}</Badge>
          </div>
        </div>
      </div>

      <div class="actions">
        <Btn @click="emit('edit')">Editar Cliente</Btn>
        <Btn variant="secondary" @click="emit('newLoan')">+ Nuevo Préstamo</Btn>
        <Btn variant="ghost" @click="emit('close')">Cerrar</Btn>
      </div>
    </div>
    </template>
  </Modal>
</template>

<style scoped>
.detail {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.header {
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

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.info-row label {
  font-size: 10px;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
}

.info-row span:first-child {
  color: #94a3b8;
  font-size: 12px;
}

.amount {
  font-family: 'Share Tech Mono', monospace;
  font-weight: 700;
}

.amount.danger {
  color: #ef4444;
}

.loans {
  margin-top: 16px;
}

.loan-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.loan-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: rgba(0,0,0,0.2);
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s;
}

.loan-item:hover {
  background: rgba(212,175,55,0.08);
}

.loan-info {
  flex: 1;
}

.loan-id {
  font-family: 'Share Tech Mono', monospace;
  font-size: 12px;
}

.loan-type {
  display: block;
  font-size: 11px;
  color: #94a3b8;
}

.loan-amount {
  font-family: 'Share Tech Mono', monospace;
  font-weight: 700;
  color: #fff;
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