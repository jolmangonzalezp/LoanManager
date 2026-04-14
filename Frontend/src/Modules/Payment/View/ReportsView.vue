<script setup>
import { ref, onMounted } from 'vue'
import PageHeader from '@/Shared/Components/PageHeader.vue'
import GCard from '@/Shared/Components/GCard.vue'
import CardTitle from '@/Shared/Components/CardTitle.vue'
import TableWrap from '@/Shared/Components/TableWrap.vue'
import { usePaymentApi } from '@/Modules/Payment'
import { formatCurrency } from '@/Shared/Composable/useApi'

const paymentApi = usePaymentApi()

const loading = ref(true)
const projected = ref([])
const collection = ref({})
const profitability = ref([])

async function loadData() {
  loading.value = true
  try {
    const [proj, coll, prof] = await Promise.all([
      paymentApi.getProjectedVsActual(),
      paymentApi.getCollectionAvailability(),
      paymentApi.getClientProfitability()
    ])
    projected.value = proj || []
    collection.value = coll?.[0] || {}
    profitability.value = prof || []
  } catch (e) {
    console.error('Error:', e)
  } finally {
    loading.value = false
  }
}

onMounted(loadData)
</script>

<template>
  <div class="page pu">
    <PageHeader title="Reportes" />

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <div class="reports-grid">
        <GCard>
          <CardTitle>Saldos Proyectados vs Reales</CardTitle>
          <TableWrap v-if="projected.length" :headers="['Cliente', 'Proyectado', 'Real', 'Diferencia']">
            <tr v-for="p in projected" :key="p.loan_id">
              <td>{{ p.customer_name }}</td>
              <td>{{ formatCurrency(p.projected_debt) }}</td>
              <td>{{ formatCurrency(p.actual_debt) }}</td>
              <td :class="{ danger: p.difference > 0 }">{{ formatCurrency(p.difference) }}</td>
            </tr>
          </TableWrap>
          <div v-else class="empty">No hay datos</div>
        </GCard>

        <GCard>
          <CardTitle>Disponibilidad de Recaudo</CardTitle>
          <div class="stat">
            <label>Capital Disponible</label>
            <value>{{ formatCurrency(collection.total_capital_available) }}</value>
          </div>
          <div class="stat">
            <label>Préstamos Activos</label>
            <div>{{ collection.active_loans }} de {{ collection.total_loans }}</div>
          </div>
        </GCard>

        <GCard>
          <CardTitle>ROI por Cliente</CardTitle>
          <TableWrap v-if="profitability.length" :headers="['Cliente', 'Total Pagado', 'Interés', 'Capital', '# Préstamos']">
            <tr v-for="p in profitability" :key="p.customer_id">
              <td>{{ p.customer_name }}</td>
              <td>{{ formatCurrency(p.total_paid) }}</td>
              <td>{{ formatCurrency(p.interest_paid) }}</td>
              <td>{{ formatCurrency(p.capital_paid) }}</td>
              <td>{{ p.loan_count }}</td>
            </tr>
          </TableWrap>
          <div v-else class="empty">No hay datos</div>
        </GCard>
      </div>
    </template>
  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.loading { text-align: center; padding: 40px; color: #94a3b8; }
.reports-grid { display: flex; flex-direction: column; gap: 16px; }
.stat { padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.07); }
.stat label { font-size: 10px; color: #d4af37; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
.stat value { font-family: 'Share Tech Mono', monospace; font-size: 20px; font-weight: 700; color: #d4af37; }
.danger { color: #ef4444 !important; }
.empty { text-align: center; padding: 20px; color: #3d4f5a; }
</style>