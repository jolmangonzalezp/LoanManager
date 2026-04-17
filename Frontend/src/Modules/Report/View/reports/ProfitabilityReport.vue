<script setup>
import { onMounted } from 'vue'
import { useReportApi } from '@/Modules/Report'
import { useDataLoader, formatCurrency } from '@/Shared'

const reportApi = useReportApi()
const { loading, data, load } = useDataLoader(() => reportApi.getProfitability())

onMounted(() => load())
</script>

<template>
  <div class="profitability-report">
    <header class="report-header">
      <h2>Rentabilidad</h2>
      <span class="subtitle">Análisis de ingresos y ROI</span>
    </header>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else-if="data">
      <section class="report-section">
        <h3 class="section-title">Resumen</h3>
        <div class="kpi-grid">
          <div class="kpi-card highlight">
            <div class="kpi-label">Total Intereses</div>
            <div class="kpi-value gold">{{ formatCurrency(data.total_intereses) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Total Capital</div>
            <div class="kpi-value">{{ formatCurrency(data.total_capital) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Ratio Intereses/Capital</div>
            <div class="kpi-value">{{ (data.ratio_intereses_capital * 100).toFixed(2) }}%</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">ROI Global</div>
            <div class="kpi-value success">{{ (data.roi_global * 100).toFixed(2) }}%</div>
          </div>
        </div>
      </section>

      <section class="report-section">
        <h3 class="section-title">Detalle por Préstamo</h3>
        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Préstamo</th>
                <th>Cliente</th>
                <th>Capital</th>
                <th>Intereses</th>
                <th>Días</th>
                <th>ROI</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="loan in data.roi_por_prestamo" :key="loan.loan_id">
                <td class="mono">{{ loan.loan_number }}</td>
                <td>{{ loan.customer_name }}</td>
                <td class="mono">{{ formatCurrency(loan.capital) }}</td>
                <td class="mono">{{ formatCurrency(loan.intereses_cobrados) }}</td>
                <td class="mono">{{ loan.dias_activo }}</td>
                <td class="mono" :class="loan.roi > 0.1 ? 'success' : ''">{{ (loan.roi * 100).toFixed(2) }}%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </template>
  </div>
</template>

<style scoped>
.profitability-report {
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

.kpi-value.gold { color: #d4af37; }
.kpi-value.success { color: #10b981; }

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
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

.mono {
  font-family: 'Share Tech Mono', monospace;
}

.success {
  color: #10b981;
}
</style>
