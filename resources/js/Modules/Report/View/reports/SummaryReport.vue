<script setup>
import { onMounted } from 'vue';
import { formatCurrency } from '@/Shared';
import { useReports } from '@/Modules/Report';

const { summary, getSummary } = useReports();

//
// const portfolio = computed(() => summary.value?.portfolio || {})
// const kpis = computed(() => summary.value?.kpis || {})
// const collection = computed(() => summary.value?.collection || {})
//
onMounted(() => getSummary());
</script>

<template>
  <div class="summary-report" v-if="summary">
    <header class="report-header">
      <h2>Resumen General</h2>
      <span class="subtitle">Overview del sistema</span>
    </header>
      <section class="report-section" v-if="summary.portfolio">
        <h3 class="section-title">Cartera de Préstamos</h3>
        <div class="kpi-grid">
          <div class="kpi-card">
            <div class="kpi-label">Total Prestado</div>
            <div class="kpi-value gold">{{ formatCurrency(summary.portfolio.totalPrestado) }}</div>
          </div>
            <div class="kpi-card">
                <div class="kpi-label">Capital Pendiente</div>
                <div class="kpi-value">{{ formatCurrency(summary.portfolio.capitalPendiente) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Intereses Cobrados</div>
                <div class="kpi-value success">{{ formatCurrency(summary.portfolio.interesesCobrados) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Préstamos Activos</div>
                <div class="kpi-value">{{ summary.portfolio.numeroPrestamosActivos }}</div>
            </div>
        </div>
      </section>
      <section class="report-section" v-if="summary.collection">
        <h3 class="section-title">Recaudación del Mes</h3>
        <div class="kpi-grid">
          <div class="kpi-card">
            <div class="kpi-label">Monto Cobrado</div>
            <div class="kpi-value gold">{{ formatCurrency(summary.collection.montoCobrado) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Cumplimiento</div>
            <div class="kpi-value" :class="summary.collection.porcentajeCumplimiento >= 100 ? 'success' : summary.collection.porcentajeCumplimiento < 50 ? 'danger' : ''">
              {{ summary.collection.porcentajeCumplimiento }}%
            </div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Pagos Registrados</div>
            <div class="kpi-value">{{ summary.collection.numeroPagos }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Total Clientes</div>
            <div class="kpi-value">{{ summary.kpis.totalClientes }}</div>
          </div>
        </div>
      </section>
      <section class="report-section" v-if="summary.kpis">
        <h3 class="section-title">KPIs Principales</h3>
        <div class="kpi-grid">
          <div class="kpi-card">
            <div class="kpi-label">Tasa de Mora</div>
            <div class="kpi-value" :class="summary.kpis.tasaMora > 10 ? 'danger' : ''">{{ summary.kpis.tasaMora }}%</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Tasa Recuperación</div>
            <div class="kpi-value success">{{ summary.kpis.tasaRecuperacion }}%</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Ticket Promedio</div>
            <div class="kpi-value">{{ formatCurrency(summary.kpis.ticketPromedio) }}</div>
          </div>
            <div class="kpi-card">
              <div class="kpi-label">Clientes Recurrentes</div>
              <div class="kpi-value">{{ summary.kpis.porcentajeClientesRecurrentes }}%</div>
            </div>
        </div>
      </section>
  </div>
</template>

<style scoped>
.summary-report {
  animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.report-header {
  margin-bottom: 24px;
}

.report-header h2 {
  font-size: 20px;
  font-weight: 700;
  color: #e2e8f0;
  margin: 0 0 4px 0;
}

.subtitle {
  font-size: 12px;
  color: #64748b;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.report-section {
  margin-bottom: 32px;
}

.section-title {
  font-size: 12px;
  font-weight: 600;
  color: #94a3b8;
  margin-bottom: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.kpi-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 12px;
  padding: 20px;
  text-align: center;
}

.kpi-label {
  font-size: 10px;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 8px;
}

.kpi-value {
  font-family: 'Share Tech Mono', monospace;
  font-size: 18px;
  font-weight: 700;
  color: #e2e8f0;
}

.kpi-value.gold { color: #d4af37; }
.kpi-value.success { color: #10b981; }
.kpi-value.danger { color: #ef4444; }

@media screen and (max-width: 674px){
  .kpi-grid{
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
  }
}

@media screen and (max-width: 430px){
  .kpi-grid{
    grid-template-columns: 1fr;
    grid-template-rows: repeat(2, 1fr);
  }
}
</style>
