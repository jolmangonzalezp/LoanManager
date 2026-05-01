<script setup>
import { PageHeader, KPI, GCard, CardTitle, Btn, useDataLoader } from '@/Shared'
import { useDashboardView } from '@/Modules/Dashboard'
import { formatCurrency } from '@/Shared'
import QuickActionsComponent from "@Shared/Components/QuickActionsComponent.vue";

const { loading, summary } = useDashboardView()
</script>

<template>
  <QuickActionsComponent />
  <div class="dashboard pu">
    <PageHeader title="Resumen de Activos"></PageHeader>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <div class="kpi-grid">
        <KPI 
          label="Total Cartera" 
          :value="formatCurrency(summary?.portfolio?.capital_pendiente || 0)" 
          sub="En pesos" 
          :goldValue="true" 
        />
        <KPI 
          label="Préstamos Activos" 
          :value="summary?.portfolio?.numero_prestamos_activos || 0" 
          sub="En cartera" 
          :goldValue="true" 
        />
        <KPI 
          label="En Mora" 
          :value="formatCurrency(summary?.delinquency?.monto_en_mora || 0)" 
          sub="Cartera vencida" 
          :goldValue="true" 
        />
      </div>

      <div class="dashboard-grid">
        <GCard>
          <CardTitle>Resumen del Mes</CardTitle>
          <div class="stats-list">
            <div class="stat-item">
              <span class="stat-label">Total Prestado</span>
              <span class="stat-value">{{ formatCurrency(summary?.portfolio?.total_prestado || 0) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Clientes</span>
              <span class="stat-value">{{ summary?.portfolio?.total_clientes || 0 }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Tasa de Mora</span>
              <span class="stat-value">{{ summary?.kpis?.tasa_mora || 0 }}%</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Clientes en Mora</span>
              <span class="stat-value">{{ summary?.delinquency?.clientes_en_mora || 0 }}</span>
            </div>
          </div>
        </GCard>

        <GCard>
          <CardTitle>Intereses</CardTitle>
          <div class="stats-list">
            <div class="stat-item">
              <span class="stat-label">Generados</span>
              <span class="stat-value">{{ formatCurrency(summary?.portfolio?.intereses_generados || 0) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Cobrados</span>
              <span class="stat-value">{{ formatCurrency(summary?.portfolio?.intereses_cobrados || 0) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Ticket Promedio</span>
              <span class="stat-value">{{ formatCurrency(summary?.kpis?.ticket_promedio || 0) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Tasa Interés Promedio</span>
              <span class="stat-value">{{ summary?.portfolio?.tasa_interes_promedio || 0 }}%</span>
            </div>
          </div>
        </GCard>
      </div>
    </template>
  </div>
</template>

<style scoped>
.dashboard { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.loading { text-align: center; padding: 40px; color: #94a3b8; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 26px; }
.dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.stats-list { display: flex; flex-direction: column; gap: 10px; margin-top: 12px; }
.stat-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 6px; }
.stat-label { font-size: 12px; color: #94a3b8; }
.stat-value { font-family: 'Share Tech Mono', monospace; font-weight: 700; color: #fff; }
</style>