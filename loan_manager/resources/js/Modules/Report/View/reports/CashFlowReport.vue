<script setup>
import { onMounted } from 'vue'
import { useReports } from '@/Modules/Report'
import { Input, formatCurrency } from '@/Shared';

const { cashFlowParams, cashFlowResponse, initCashFlow, cashFlow } = useReports();

const onChange = () => {
    cashFlow(cashFlowParams.value)
}

onMounted(async () => {
    initCashFlow();
    await cashFlow(cashFlowParams.value);
})
</script>

<template>
  <div class="cashflow-report">
    <header class="report-header">
      <h2>Flujo de Caja</h2>
      <span class="subtitle">Ingresos y egresos en un período</span>
    </header>
    <div class="filters">
      <div class="filter-group">
          <Input label="Desde" type="date" v-model="cashFlowParams.fechaInicio" @change="onChange"/>
      </div>
      <div class="filter-group">
          <Input label="Hasta" type="date" v-model="cashFlowParams.fechaFin" @change="onChange"/>
      </div>
    </div>
      <section class="report-section">
        <h3 class="section-title">Resumen</h3>
        <div class="kpi-grid">
          <div class="kpi-card success">
            <div class="kpi-label">Ingresos por Pagos</div>
            <div class="kpi-value success">{{ formatCurrency(cashFlowResponse?.ingresos) }}</div>
          </div>
          <div class="kpi-card danger">
            <div class="kpi-label">Egresos por Desembolsos</div>
            <div class="kpi-value danger">{{ formatCurrency(cashFlowResponse?.egresos) }}</div>
          </div>
          <div class="kpi-card highlight">
            <div class="kpi-label">Flujo Neto</div>
            <div class="kpi-value" :class="cashFlowResponse?.flujoNeto >= 0 ? 'success' : 'danger'">
              {{ formatCurrency(cashFlowResponse?.flujoNeto) }}
            </div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Total Pagos</div>
            <div class="kpi-value">{{ cashFlowResponse?.totalPagos }}</div>
          </div>
        </div>
      </section>

      <section class="report-section">
        <h3 class="section-title">Detalle</h3>
        <div class="kpi-grid">
          <div class="kpi-card">
            <div class="kpi-label">Total Desembolsos</div>
            <div class="kpi-value">{{ cashFlowResponse?.totalDesembolsos }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Fecha Inicio</div>
            <div class="kpi-value">{{ cashFlowResponse?.fechaInicio }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Fecha Fin</div>
            <div class="kpi-value">{{ cashFlowResponse?.fechaFin }}</div>
          </div>
        </div>
      </section>
  </div>
</template>

<style scoped>
.cashflow-report {
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

.filters {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
  padding: 16px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 8px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.filter-group label {
  font-size: 10px;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.filter-group input {
  padding: 8px 12px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 6px;
  background: rgba(0, 0, 0, 0.3);
  color: #e2e8f0;
  font-size: 13px;
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

.kpi-card.success {
  border-color: rgba(16, 185, 129, 0.3);
  background: rgba(16, 185, 129, 0.05);
}

.kpi-card.danger {
  border-color: rgba(239, 68, 68, 0.3);
  background: rgba(239, 68, 68, 0.05);
}

.kpi-card.highlight {
  border-color: rgba(212, 175, 55, 0.3);
  background: rgba(212, 175, 55, 0.05);
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
