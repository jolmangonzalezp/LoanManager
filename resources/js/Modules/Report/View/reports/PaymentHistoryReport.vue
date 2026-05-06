<script setup>
import { onMounted } from 'vue'
import { useReportApi } from '@/Modules/Report'
import { useDataLoader, formatCurrency } from '@/Shared'

const reportApi = useReportApi()
const { loading, data, load } = useDataLoader(() => reportApi.getPaymentHistory())

onMounted(() => load())
</script>

<template>
  <div class="payment-history-report">
    <header class="report-header">
      <h2>Historial de Pagos</h2>
      <span class="subtitle">{{ data?.total || 0 }} pagos - {{ formatCurrency(data?.monto_total || 0) }}</span>
    </header>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else-if="data">
      <section class="report-section">
        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Préstamo</th>
                <th>Cliente</th>
                <th>Monto</th>
                <th>Interés</th>
                <th>Capital</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="payment in data.pagos" :key="payment.id">
                <td>{{ payment.fecha_pago }}</td>
                <td class="mono">{{ payment.loan_number }}</td>
                <td>{{ payment.customer_name }}</td>
                <td class="mono">{{ formatCurrency(payment.monto) }}</td>
                <td class="mono">{{ formatCurrency(payment.interes_pagado) }}</td>
                <td class="mono">{{ formatCurrency(payment.capital_pagado) }}</td>
                <td><span class="badge" :class="payment.estado">{{ payment.estado }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </template>
  </div>
</template>

<style scoped>
.payment-history-report {
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

.badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
}

.badge.applied {
  background: rgba(16, 185, 129, 0.2);
  color: #10b981;
}

.badge.pending {
  background: rgba(234, 179, 8, 0.2);
  color: #eab308;
}

.badge.failed {
  background: rgba(239, 68, 68, 0.2);
  color: #ef4444;
}
</style>
