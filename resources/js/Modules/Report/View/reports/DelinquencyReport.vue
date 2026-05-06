<script setup lang="ts">
import { useReports } from '@/Modules/Report';
import { onMounted } from 'vue';
import { DataTable, formatCurrency } from '@/Shared';

const { columnsDeliquency, loanDelinquency ,deliquencyReport, getDeliquency } = useReports();

onMounted(() => {
    getDeliquency();
})
</script>

<template>
  <div class="delinquency-report">
    <header class="report-header">
      <h2>Mora</h2>
      <span class="subtitle">Préstamos en mora</span>
    </header>
      <section class="report-section" v-if="deliquencyReport">
        <h3 class="section-title">Resumen de Mora</h3>
        <div class="kpi-grid">
          <div class="kpi-card danger">
            <div class="kpi-label">Clientes en Mora</div>
            <div class="kpi-value danger">{{ deliquencyReport.clientesEnMora }}</div>
          </div>
          <div class="kpi-card danger">
            <div class="kpi-label">Monto en Mora</div>
            <div class="kpi-value danger">{{ formatCurrency(deliquencyReport.montoEnMora) }}</div>
          </div>
          <div class="kpi-card danger">
            <div class="kpi-label">Cartera Vencida</div>
            <div class="kpi-value danger">{{ deliquencyReport.porcentajeCarteraVencida }}%</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Días Promedio Atraso</div>
            <div class="kpi-value">{{ deliquencyReport.diasPromedioAtraso }} días</div>
          </div>
        </div>
      </section>
      <section class="report-section">
        <h3 class="section-title">Detalle de Mora</h3>
        <div class="table-container">
            <DataTable :columns="columnsDeliquency" :rows="loanDelinquency" />
        </div>
      </section>
  </div>
</template>

<style scoped>
.delinquency-report {
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

.kpi-card.danger {
  border-color: rgba(239, 68, 68, 0.3);
  background: rgba(239, 68, 68, 0.05);
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

.kpi-value.danger { color: #ef4444; }

.table-container {
  overflow-x: auto;
}

.data-table th {
  text-align: left;
  padding: 12px 16px;
  font-size: 10px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.data-table td {
  padding: 12px 16px;
  color: #e2e8f0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.data-table tr:hover td {
  background: rgba(255, 255, 255, 0.02);
}

.danger {
  color: #ef4444;
}

.empty-state p {
  margin: 0;
  font-size: 14px;
}

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
