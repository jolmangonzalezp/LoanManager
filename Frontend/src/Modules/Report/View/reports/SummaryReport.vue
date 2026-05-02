<script setup>
import { computed, onMounted } from 'vue'
import { useReportApi } from '@/Modules/Report'
import { useDataLoader, formatCurrency } from '@/Shared'

const reportApi = useReportApi()

const { loading, data: summary, load: loadSummary } = useDataLoader(() => reportApi.getSummary())

const portfolio = computed(() => summary.value?.portfolio || {})
const kpis = computed(() => summary.value?.kpis || {})
const collection = computed(() => summary.value?.collection || {})

onMounted(() => loadSummary())
</script>

<template>
  <div class="summary-report">
    <header class="report-header">
      <h2>Resumen General</h2>
      <span class="subtitle">Overview del sistema</span>
    </header>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <section class="report-section">
        <h3 class="section-title">Cartera de Préstamos</h3>
        <div class="kpi-grid">
          <div class="kpi-card">
            <div class="kpi-label">Total Prestado</div>
            <div class="kpi-value gold">{{ formatCurrency(portfolio.total_prestado) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Capital Pendiente</div>
            <div class="kpi-value">{{ formatCurrency(portfolio.capital_pendiente) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Intereses Cobrados</div>
            <div class="kpi-value success">{{ formatCurrency(portfolio.intereses_cobrados) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Préstamos Activos</div>
            <div class="kpi-value">{{ portfolio.numero_prestamos_activos }}</div>
          </div>
        </div>
      </section>

      <section class="report-section">
        <h3 class="section-title">Recaudación del Mes</h3>
        <div class="kpi-grid">
          <div class="kpi-card">
            <div class="kpi-label">Monto Cobrado</div>
            <div class="kpi-value gold">{{ formatCurrency(collection.monto_cobrado) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Cumplimiento</div>
            <div class="kpi-value" :class="collection.porcentaje_cumplimiento >= 100 ? 'success' : collection.porcentaje_cumplimiento < 50 ? 'danger' : ''">
              {{ collection.porcentaje_cumplimiento }}%
            </div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Pagos Registrados</div>
            <div class="kpi-value">{{ collection.numero_pagos }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Total Clientes</div>
            <div class="kpi-value">{{ kpis.total_clientes }}</div>
          </div>
        </div>
      </section>

      <section class="report-section">
        <h3 class="section-title">KPIs Principales</h3>
        <div class="kpi-grid">
          <div class="kpi-card">
            <div class="kpi-label">Tasa de Mora</div>
            <div class="kpi-value" :class="kpis.tasa_mora > 10 ? 'danger' : ''">{{ kpis.tasa_mora }}%</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Tasa Recuperación</div>
            <div class="kpi-value success">{{ kpis.tasa_recuperacion }}%</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Ticket Promedio</div>
            <div class="kpi-value">{{ formatCurrency(kpis.ticket_promedio) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Clientes Recurrentes</div>
            <div class="kpi-value">{{ kpis.porcentaje_clientes_recurrentes }}%</div>
          </div>
        </div>
      </section>
    </template>
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
