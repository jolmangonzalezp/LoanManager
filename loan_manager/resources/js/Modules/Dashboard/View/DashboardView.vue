<script setup lang="ts">
import { onMounted } from 'vue';
import { useDashboard } from '@/Modules/Dashboard/index.ts';
import { PageHeader, KPI, GCard, CardTitle, QuickActions, formatCurrency } from '@/Shared'

const { summary, getSummary } = useDashboard()
onMounted(() => {
    getSummary()
})
</script>

<template>
  <QuickActions />
  <div class="dashboard pu">
    <PageHeader title="Resumen de Activos"></PageHeader>

      <div class="kpi-grid" v-if="summary?.portfolio">
        <KPI
          label="Total Cartera"
          :value="formatCurrency(summary?.portfolio?.capitalPendiente || 0)"
          sub="En pesos"
          :goldValue="true"
          class="kpi-grid-item"
        />
        <KPI
          label="Préstamos Activos"
          :value="summary?.portfolio?.numeroPrestamosActivos.toString() || '0'"
          sub="En cartera"
          :goldValue="true"
          class="kpi-grid-item"
        />
        <KPI
          label="En Mora"
          :value="formatCurrency(summary?.delinquency?.montoEnMora || 0)"
          sub="Cartera vencida"
          :goldValue="true"
          class="kpi-grid-item"
        />
      </div>

      <div class="dashboard-grid">
        <GCard class="dashboard-grid-item">
          <CardTitle>Resumen del Mes</CardTitle>
          <div class="stats-list">
            <div class="stat-item">
              <span class="stat-label">Total Prestado</span>
              <span class="stat-value">{{ formatCurrency(summary?.portfolio?.totalPrestado || 0) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Clientes</span>
              <span class="stat-value">{{ summary?.portfolio?.totalClientes || 0 }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Tasa de Mora</span>
              <span class="stat-value">{{ summary?.kpis?.tasaMora || 0 }}%</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Clientes en Mora</span>
              <span class="stat-value">{{ summary?.delinquency?.clientesEnMora || 0 }}</span>
            </div>
          </div>
        </GCard>

        <GCard class="dashboard-grid-item">
          <CardTitle>Intereses</CardTitle>
          <div class="stats-list">
            <div class="stat-item">
              <span class="stat-label">Generados</span>
              <span class="stat-value">{{ formatCurrency(summary?.portfolio?.interesesGenerados || 0) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Cobrados</span>
              <span class="stat-value">{{ formatCurrency(summary?.portfolio?.interesesCobrados || 0) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Ticket Promedio</span>
              <span class="stat-value">{{ formatCurrency(summary?.kpis?.ticketPromedio || 0) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Tasa Interés Promedio</span>
              <span class="stat-value">{{ summary?.portfolio?.tasaInteresPromedio || 0 }}%</span>
            </div>
          </div>
        </GCard>
      </div>
  </div>
</template>

<style scoped>
.dashboard { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; overflow-y: auto}
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 26px; }
.dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.stats-list { display: flex; flex-direction: column; gap: 10px; margin-top: 12px; }
.stat-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 6px; }
.stat-label { font-size: 12px; color: #94a3b8; }
.stat-value { font-family: 'Share Tech Mono', monospace; font-weight: 700; color: #fff; }

@media screen and (max-width: 663px){
  .kpi-grid{
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
  }
  .kpi-grid-item:first-child{
    grid-column: 1/3;
  }
  .kpi-grid-item:nth-child(2){
    grid-column: 1/2;
    grid-row: 2/2;
  }
  .kpi-grid-item:last-child{
    grid-column: 2/2;
    grid-row: 2/2;
  }
  .dashboard-grid{
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
  }

  .dashboard-grid-item {
    grid-column: 1/3;
  }
}

@media screen and (max-width: 430px){
  .kpi-grid{
    grid-template-columns: 1fr;
    grid-template-rows: repeat(2, 1fr);
  }
}
</style>
