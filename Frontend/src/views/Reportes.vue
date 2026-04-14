<script setup>
import { ref, onMounted } from 'vue'
import PageHeader from '../components/ui/PageHeader.vue'
import GCard from '../components/ui/GCard.vue'
import CardTitle from '../components/ui/CardTitle.vue'
import KPI from '../components/ui/KPI.vue'
import TableWrap from '../components/ui/TableWrap.vue'
import Amount from '../components/ui/Amount.vue'
import Ref from '../components/ui/Ref.vue'
import { useApi, formatCurrency, formatDate } from '../composables/useApi'

const api = useApi()

const loading = ref(true)

const projected = ref([])
const collection = ref({})
const profitability = ref([])

const stats = ref({
  capital: 0,
  activeLoans: 0,
  totalLoans: 0
})

async function loadReports() {
  loading.value = true
  try {
    const [projectedData, collectionData, profitabilityData] = await Promise.all([
      api.get('/reports/projected-vs-actual').catch(() => []),
      api.get('/reports/collection-availability').catch(() => []),
      api.get('/reports/client-profitability').catch(() => [])
    ])

    projected.value = projectedData || []
    collection.value = collectionData[0] || {}
    profitability.value = profitabilityData || []

    stats.value = {
      capital: collection.value.total_capital_available || 0,
      activeLoans: collection.value.active_loans || 0,
      totalLoans: collection.value.total_loans || 0
    }
  } catch (e) {
    console.error('Error loading reports:', e)
  } finally {
    loading.value = false
  }
}

onMounted(loadReports)
</script>

<template>
  <div class="reportes pu">
    <PageHeader title="Reportes" />

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <div class="kpi-grid">
        <KPI label="Capital Disponible" :value="formatCurrency(stats.capital)" sub="Para recaudo" :goldValue="true" />
        <KPI label="Clientes Activos" :value="stats.activeLoans" sub="con préstamos" :goldValue="true" />
        <KPI label="Total Préstamos" :value="stats.totalLoans" sub="Registrados" :goldValue="true" />
      </div>

      <div class="reports-grid">
        <GCard>
          <CardTitle>Saldos Proyectados vs Reales</CardTitle>
          <TableWrap v-if="projected.length > 0" :headers="['Cliente', 'Proyectado', 'Real', 'Diferencia', 'Fecha']">
            <tr v-for="row in projected" :key="row.loan_id">
              <td style="color: #fff">{{ row.customer_name || 'Cliente' }}</td>
              <td><Amount>{{ formatCurrency(row.projected_debt) }}</Amount></td>
              <td><Amount>{{ formatCurrency(row.actual_debt) }}</Amount></td>
              <td>
                <Amount :color="row.difference > 0 ? '#ef4444' : '#10b981'">
                  {{ formatCurrency(row.difference) }}
                </Amount>
              </td>
              <td style="color: #94a3b8">{{ formatDate(row.as_of_date) }}</td>
            </tr>
          </TableWrap>
          <div v-else class="empty">No hay datos</div>
        </GCard>

        <GCard>
          <CardTitle>Disponibilidad de Recaudo</CardTitle>
          <div class="stat">
            <label>Capital Disponible</label>
            <div class="value">{{ formatCurrency(collection.total_capital_available) }}</div>
          </div>
          <div class="stat">
            <label>Préstamos Activos</label>
            <div>{{ collection.active_loans }} de {{ collection.total_loans }}</div>
          </div>
          <div class="stat">
            <label>Moneda</label>
            <div>{{ collection.currency || 'COP' }}</div>
          </div>
        </GCard>

        <GCard style="grid-column: 1 / -1">
          <CardTitle>ROI por Cliente</CardTitle>
          <TableWrap v-if="profitability.length > 0" :headers="['Cliente', 'Total Pagado', 'Interés', 'Capital', '# Préstamos']">
            <tr v-for="row in profitability" :key="row.customer_id">
              <td style="color: #fff">{{ row.customer_name || 'Cliente' }}</td>
              <td><Amount color="#10b981">{{ formatCurrency(row.total_paid) }}</Amount></td>
              <td><Amount>{{ formatCurrency(row.interest_paid) }}</Amount></td>
              <td><Amount>{{ formatCurrency(row.capital_paid) }}</Amount></td>
              <td style="color: #94a3b8">{{ row.loan_count }}</td>
            </tr>
          </TableWrap>
          <div v-else class="empty">No hay datos</div>
        </GCard>
      </div>
    </template>
  </div>
</template>

<style scoped>
.reportes {
  animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 26px;
}

.reports-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.stat {
  padding: 10px 0;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.stat label {
  font-size: 10px;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
}

.stat .value {
  font-family: 'Share Tech Mono', monospace;
  font-size: 24px;
  font-weight: 700;
  color: #d4af37;
  margin-top: 4px;
}

.stat:last-child {
  border-bottom: none;
}

.empty {
  text-align: center;
  padding: 20px;
  color: #94a3b8;
  font-size: 13px;
}
</style>