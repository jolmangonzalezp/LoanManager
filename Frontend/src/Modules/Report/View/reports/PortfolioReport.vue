<script setup>
import { ref, onMounted } from 'vue'
import { useReports } from '@/Modules/Report'
import { formatCurrency } from '@/Shared'

const { porfolioReport, getPortfolio } = useReports()

onMounted(async () => {
  await getPortfolio()
})
</script>

<template>
  <div class="portfolio-report">
    <header class="report-header">
      <h2>Cartera de Préstamos</h2>
      <span class="subtitle">Estado actual de la cartera</span>
    </header>

    <div v-if="!porfolioReport" class="loading">Cargando...</div>

    <template v-else>
      <section class="report-section">
        <h3 class="section-title">Métricas Principales</h3>
        <div class="kpi-grid">
          <div class="kpi-card highlight">
            <div class="kpi-label">Total Prestado</div>
            <div class="kpi-value gold">{{ formatCurrency(porfolioReport.total_prestado) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Capital Pendiente</div>
            <div class="kpi-value">{{ formatCurrency(porfolioReport.capital_pendiente) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Intereses Generados</div>
            <div class="kpi-value">{{ formatCurrency(porfolioReport.intereses_generados) }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Intereses Cobrados</div>
            <div class="kpi-value success">{{ formatCurrency(porfolioReport.intereses_cobrados) }}</div>
          </div>
        </div>
      </section>
      <section class="report-section">
        <h3 class="section-title">Distribución</h3>
        <div class="kpi-grid">
          <div class="kpi-card">
            <div class="kpi-label">Préstamos Activos</div>
            <div class="kpi-value">{{ porfolioReport.numero_prestamos_activos }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Total Clientes</div>
            <div class="kpi-value">{{ porfolioReport.total_clientes }}</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">Tasa Interés Promedio</div>
            <div class="kpi-value">{{ porfolioReport.tasa_interes_promedio }}%</div>
          </div>
          <div class="kpi-card">
            <div class="kpi-label">% Recuperado</div>
            <div class="kpi-value success">
              {{ porfolioReport.total_prestado > 0 ? ((1 - porfolioReport.capital_pendiente / porfolioReport.total_prestado) * 100).toFixed(1) : 0 }}%
            </div>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>

<style scoped>
.portfolio-report {
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