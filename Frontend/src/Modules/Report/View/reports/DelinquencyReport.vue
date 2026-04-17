<script setup>
import { onMounted } from 'vue'
import { useReportApi } from '@/Modules/Report'
import { useDataLoader, formatCurrency } from '@/Shared'

const reportApi = useReportApi()
const { loading, data, load } = useDataLoader(() => reportApi.getDelinquency())

onMounted(() => load())
</script>

<template>
  <div class="delinquency-report">
    <header class="report-header">
      <h2>Mora</h2>
      <span class="subtitle">Préstamos en mora</span>
    </header>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else-if="data">
      <section class="report-section">
        <h3 class="section-title">Resumen de Mora</h3>
        <div class="kpi-grid">
          <div class="kpi-card danger">
            <div class="kpi-label">Clientes en Mora</div>
            <div class="kpi-value danger">{{ data.clientes_en_mora }}</div>
          </div>
          <div class="kpi-card danger">
            <div class="kpi-label">Monto en Mora</div>
            <div class="kpi-value danger">{{ formatCurrency(data.monto_en_mora) }}</div>
          </div>
          <div class="kpi-card danger">
            <div class="kpi-label">Cartera Vencida</div>
            <div class="kpi-value danger">{{ data.porcentaje_cartera_vencida }}%</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Días Promedio Atraso</div>
            <div class="kpi-value">{{ data.dias_promedio_atraso }} días</div>
          </div>
        </div>
      </section>

      <section v-if="data.detalle_mora?.length > 0" class="report-section">
        <h3 class="section-title">Detalle de Mora</h3>
        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Préstamo</th>
                <th>Cliente</th>
                <th>Saldo Pendiente</th>
                <th>Días Atraso</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="loan in data.detalle_mora" :key="loan.loan_id">
                <td class="mono">{{ loan.loan_number }}</td>
                <td>{{ loan.customer_name }}</td>
                <td class="mono">{{ formatCurrency(loan.saldo_pendiente) }}</td>
                <td class="mono danger">{{ loan.dias_atraso }} días</td>
                <td><span class="badge danger">Mora</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <div v-else class="empty-state">
        <span class="empty-icon">✅</span>
        <p>No hay préstamos en mora</p>
      </div>
    </template>
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

.danger {
  color: #ef4444;
}

.badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
}

.badge.danger {
  background: rgba(239, 68, 68, 0.2);
  color: #ef4444;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #64748b;
}

.empty-icon {
  font-size: 48px;
  display: block;
  margin-bottom: 16px;
}

.empty-state p {
  margin: 0;
  font-size: 14px;
}
</style>
