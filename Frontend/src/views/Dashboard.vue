<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '../components/ui/PageHeader.vue'
import GCard from '../components/ui/GCard.vue'
import CardTitle from '../components/ui/CardTitle.vue'
import KPI from '../components/ui/KPI.vue'
import Badge from '../components/ui/Badge.vue'
import AlertBanner from '../components/ui/AlertBanner.vue'
import TableWrap from '../components/ui/TableWrap.vue'
import Amount from '../components/ui/Amount.vue'
import Ref from '../components/ui/Ref.vue'
import Btn from '../components/ui/Btn.vue'
import { useApi, formatCurrency, getStatusLabel, getStatusColor } from '../composables/useApi'

const router = useRouter()
const api = useApi()

const loading = ref(true)
const stats = ref({
  capital: 0,
  rate: 0,
  activeLoans: 0,
  collected: 0,
  collectionRate: 0,
  mora: 0,
  moraImprovement: 0
})

const priorityLoans = ref([])
const collectionData = ref({ totalCapital: 0, activeLoans: 0, totalLoans: 0 })
const overduePayments = ref([])

async function loadData() {
  loading.value = true
  try {
    const [loansReport, collection, projected] = await Promise.all([
      api.get('/loans/report').catch(() => null),
      api.get('/reports/collection-availability').catch(() => null),
      api.get('/reports/projected-vs-actual').catch(() => null)
    ])

    if (loansReport) {
      stats.value = {
        capital: loansReport.total_capital || loansReport.totalRemainingDebt || 0,
        rate: 0,
        activeLoans: loansReport.active_loans || 0,
        collected: 0,
        collectionRate: 0,
        mora: loansReport.defaulted_loans ? Math.round((loansReport.defaulted_loans / loansReport.total_loans) * 100) : 0,
        moraImprovement: 0
      }
    }

    if (collection) {
      collectionData.value = collection[0] || {}
    }

    if (projected) {
      overduePayments.value = projected.filter(p => p.difference > 0).slice(0, 4)
      priorityLoans.value = projected.slice(0, 4).map(p => ({
        id: p.loan_id || '#' + p.loan_id?.slice(0, 8),
        client: p.customer_name || 'Cliente',
        amount: p.actual_debt || 0,
        status: p.difference > 0 ? 'overdue' : 'active',
        label: getStatusLabel(p.difference > 0 ? 'overdue' : 'active')
      }))
    }
  } catch (e) {
    console.error('Error loading dashboard:', e)
  } finally {
    loading.value = false
  }
}

onMounted(loadData)
</script>

<template>
  <div class="dashboard pu">
    <PageHeader title="Resumen de Activos">
      <template #action>
        <Btn @click="router.push('/prestamos/new')">Nueva Operación</Btn>
      </template>
    </PageHeader>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <AlertBanner v-if="overduePayments.length > 0" type="danger" :clickable="true" @click="router.push('/pagos')">
        <span style="margin-right: 4px">▲</span>
        <strong>{{ overduePayments.length }} cuotas vencidas</strong>
        <span style="margin: 0 4px">·</span>
        {{ formatCurrency(overduePayments.reduce((sum, p) => sum + (p.difference || 0), 0)) }} pendiente
        <span style="margin-left: auto; font-size: 11px; opacity: 0.6">ver →</span>
      </AlertBanner>

      <div class="kpi-grid-large">
        <KPI 
          label="Capital Bajo Gestión" 
          :value="formatCurrency(stats.capital)" 
          :sub="collectionData.activeLoans ? `▲ +${stats.activeLoans} préstamos` : ''"
          :goldValue="true"
          @click="router.push('/prestamos')"
        />
        <KPI 
          label="Cartera Activa" 
          :value="stats.activeLoans" 
          sub="préstamos"
          :goldValue="true"
          @click="router.push('/prestamos')"
        />
      </div>

      <div class="kpi-grid">
        <KPI label="Préstamos Activos" :value="stats.activeLoans" sub="en cartera" :goldValue="true" />
        <KPI label="Cobrado" :value="formatCurrency(stats.collected)" :trend="stats.collectionRate ? `▲ +${stats.collectionRate}%` : ''" :trendUp="stats.collectionRate > 0" @click="router.push('/pagos')" />
        <KPI label="Índice de Mora" :value="stats.mora + '%'" :trend="stats.moraImprovement ? `▼ ${stats.moraImprovement}pt` : ''" :trendUp="stats.moraImprovement > 0" @click="router.push('/pagos')" />
      </div>

      <div class="content-grid">
        <GCard>
          <CardTitle>Préstamos de Alta Prioridad</CardTitle>
          <TableWrap v-if="priorityLoans.length > 0" :headers="['Referencia', 'Cliente', 'Monto', 'Estado', '']">
            <tr 
              v-for="loan in priorityLoans" 
              :key="loan.id"
              class="trow"
              @click="router.push('/prestamos/' + loan.id)"
            >
              <td><Ref>{{ loan.id }}</Ref></td>
              <td style="color: #fff">{{ loan.client }}</td>
              <td>
                <Amount :color="loan.status === 'overdue' ? '#f87171' : '#10b981'">
                  {{ formatCurrency(loan.amount) }}
                </Amount>
              </td>
              <td><Badge :type="getStatusColor(loan.status)">{{ loan.label }}</Badge></td>
              <td><Btn size="sm" variant="ghost">cobrar</Btn></td>
            </tr>
          </TableWrap>
          <div v-else class="empty">No hay préstamos activos</div>
        </GCard>

        <div class="side-cards">
          <GCard>
            <CardTitle>Cartera</CardTitle>
            <div class="ring-chart">
              <div class="ring">
                <div class="ring-center"></div>
              </div>
              <div class="ring-legend">
                <div>
                  <span class="dot" style="background: #d4af37"></span>Activos 
                  <span>{{ collectionData.totalLoans ? Math.round((collectionData.activeLoans / collectionData.totalLoans) * 100) : 0 }}%</span>
                </div>
                <div>
                  <span class="dot" style="background: #ef4444"></span>En mora 
                  <span>{{ stats.mora }}%</span>
                </div>
              </div>
            </div>
          </GCard>
          <GCard>
            <CardTitle>Actividad Reciente</CardTitle>
            <div class="timeline">
              <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                  <div class="timeline-title">Sistema conectado</div>
                  <div class="timeline-sub">Listo para operar</div>
                </div>
              </div>
            </div>
          </GCard>
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.dashboard {
  animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.empty {
  text-align: center;
  padding: 20px;
  color: #3d4f5a;
  font-size: 13px;
}

.kpi-grid-large {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 18px;
  margin-bottom: 18px;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 26px;
}

.content-grid {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 18px;
}

.side-cards {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.ring-chart {
  display: flex;
  align-items: center;
  gap: 24px;
}

.ring {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  background: conic-gradient(#d4af37 0% 70%, #ef4444 70% 100%);
  box-shadow: 0 0 24px rgba(212,175,55,0.25);
  position: relative;
}

.ring-center {
  position: absolute;
  width: 62px;
  height: 62px;
  background: rgba(6,9,15,0.9);
  backdrop-filter: blur(4px);
  border-radius: 50%;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 1px solid rgba(255,255,255,0.07);
}

.ring-legend {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.ring-legend > div {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11.5px;
}

.ring-legend span:last-child {
  color: #e8c84a;
  font-family: 'Share Tech Mono', monospace;
  font-size: 12px;
}

.dot {
  width: 7px;
  height: 7px;
  border-radius: 2px;
  flex-shrink: 0;
}

.timeline {
  display: flex;
  flex-direction: column;
}

.timeline-item {
  display: flex;
  gap: 12px;
  padding: 11px 0;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.timeline-item:last-child {
  border-bottom: none;
}

.timeline-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 7px;
  background: #10b981;
  box-shadow: 0 0 8px #10b98166;
}

.timeline-content {
  flex: 1;
}

.timeline-title {
  font-size: 13px;
  color: #fff;
}

.timeline-sub {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 2px;
}

.trow {
  cursor: pointer;
}

.trow:hover > td {
  background: rgba(212,175,55,0.04) !important;
}
</style>