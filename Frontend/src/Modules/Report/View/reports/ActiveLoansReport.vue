<script setup>
import { onMounted } from 'vue'
import { useReportApi } from '@/Modules/Report'
import { useDataLoader, formatCurrency } from '@/Shared'

const reportApi = useReportApi()
const { loading, data, load } = useDataLoader(() => reportApi.getActiveLoans())

onMounted(() => load())
</script>

<template>
  <div class="active-loans-report">
    <header class="report-header">
      <h2>Préstamos Activos</h2>
      <span class="subtitle">{{ data?.total || 0 }} préstamos - {{ formatCurrency(data?.total_saldo || 0) }} en saldo</span>
    </header>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else-if="data">
      <section class="report-section">
        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Préstamo</th>
                <th>Cliente</th>
                <th>Capital</th>
                <th>Saldo</th>
                <th>Tasa</th>
                <th>Fecha</th>
                <th>Próximo Pago</th>
                <th>Días</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="loan in data.prestamos" :key="loan.id">
                <td class="mono">{{ loan.loan_number }}</td>
                <td>{{ loan.customer_name }}</td>
                <td class="mono">{{ formatCurrency(loan.capital_original) }}</td>
                <td class="mono">{{ formatCurrency(loan.saldo_pendiente) }}</td>
                <td class="mono">{{ loan.tasa_interes }}%</td>
                <td>{{ loan.fecha_desembolso }}</td>
                <td>{{ loan.proximo_pago }}</td>
                <td class="mono">{{ loan.dias_activo }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </template>
  </div>
</template>

<style scoped>
.active-loans-report {
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
</style>
