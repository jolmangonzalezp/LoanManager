<script setup>
import { onMounted } from 'vue'
import { useReportApi } from '@/Modules/Report'
import { useDataLoader } from '@/Shared'

const reportApi = useReportApi()
const { loading, data, load } = useDataLoader(() => reportApi.getAudit())

onMounted(() => load())
</script>

<template>
  <div class="audit-report">
    <header class="report-header">
      <h2>Auditoría</h2>
      <span class="subtitle">Registro de cambios en el sistema</span>
    </header>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else-if="data">
      <section class="report-section">
        <h3 class="section-title">Total Registros: {{ data.total_registros }}</h3>
        
        <div v-if="data.registros?.length > 0" class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Acción</th>
                <th>Entidad</th>
                <th>ID</th>
                <th>Cambios</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="entry in data.registros" :key="entry.id">
                <td class="mono">{{ entry.fecha }}</td>
                <td><span class="badge" :class="entry.accion">{{ entry.accion }}</span></td>
                <td>{{ entry.entidad }}</td>
                <td class="mono">{{ entry.entidad_id }}</td>
                <td class="changes-cell">
                  <div v-for="(change, key) in entry.datos_nuevos" :key="key" class="change-item">
                    <span class="change-key">{{ key }}:</span>
                    <span class="change-old" v-if="entry.datos_anteriores?.[key]">{{ entry.datos_anteriores[key] }}</span>
                    <span class="change-arrow">→</span>
                    <span class="change-new">{{ change }}</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="empty-state">
          <span class="empty-icon">📝</span>
          <p>No hay registros de auditoría</p>
        </div>
      </section>
    </template>
  </div>
</template>

<style scoped>
.audit-report {
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
  vertical-align: top;
}

.data-table tr:hover td {
  background: rgba(255, 255, 255, 0.02);
}

.mono {
  font-family: 'Share Tech Mono', monospace;
  font-size: 12px;
}

.badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
}

.badge.created {
  background: rgba(16, 185, 129, 0.2);
  color: #10b981;
}

.badge.updated {
  background: rgba(234, 179, 8, 0.2);
  color: #eab308;
}

.badge.deleted {
  background: rgba(239, 68, 68, 0.2);
  color: #ef4444;
}

.changes-cell {
  max-width: 400px;
}

.change-item {
  margin-bottom: 4px;
  font-size: 12px;
}

.change-key {
  color: #64748b;
  margin-right: 4px;
}

.change-old {
  color: #ef4444;
  text-decoration: line-through;
}

.change-arrow {
  color: #64748b;
  margin: 0 4px;
}

.change-new {
  color: #10b981;
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
