<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { RouteDetail, RouteForms, ZoneForm, useRoute, RouteService } from '@/Modules/Route';
import { Btn, DataTable, KPI, PageHeader, GCard, CardTitle, useModal } from '@/Shared';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faTrash } from '@fortawesome/free-solid-svg-icons';
import { useAuth } from '@/Modules/Auth';
import MapboxMap from '@/Modules/Geo/Components/MapboxMap.vue';
import type { MapData } from '@/Modules/Route';

const { hasRole } = useAuth()
const isAdmin = hasRole('admin')

const {
  columns,
  routes,
  zones,
  summary,
  getAll,
  getById,
  getAllZones,
  removeZone,
  fillZone,
} = useRoute()

const { open } = useModal()
const activeTab = ref<'routes' | 'zones' | 'mapa'>('mapa')
const mapData = ref<MapData | null>(null)
const mapLoading = ref(false)
const mapError = ref(false)

const loadMapData = async () => {
  if (mapData.value) return
  mapLoading.value = true
  mapError.value = false
  try {
    mapData.value = await RouteService.getMapData()
  } catch {
    mapError.value = true
    mapData.value = null
  } finally {
    mapLoading.value = false
  }
}

watch(activeTab, (tab) => {
  if (tab === 'mapa') {
    loadMapData()
  }
})

const handleRowClick = async (id: string) => {
  await getById(id);
  open(RouteDetail, { size: 'lg' });
}

const handleCreateRoute = () => {
  open(RouteForms, { size: 'lg' });
}

const handleCreateZone = () => {
  open(ZoneForm, { size: 'lg' });
}

const handleEditZone = (id: string) => {
  fillZone(id)
  open(ZoneForm, {
    size: 'lg',
    props: { id },
  })
}

const handleDeleteZone = async (id: string) => {
  if (confirm('¿Eliminar esta zona?')) {
    await removeZone(id)
  }
}

onMounted(async () => {
  if (isAdmin) {
    await getAll()
    await getAllZones()
  }
  loadMapData()
})
</script>

<template>
  <div class="page pu">
    <PageHeader title="Rutas y Zonas" />

    <div v-if="isAdmin" class="kpi-grid">
      <KPI label="Total Rutas" :value="summary.total" sub="Registradas" :goldValue="true" class="kpi-grid-item"/>
      <KPI label="Total Zonas" :value="zones.length" sub="Registradas" :goldValue="true" class="kpi-grid-item"/>
    </div>

    <div class="tabs">
      <button class="tab" :class="{ active: activeTab === 'mapa' }" @click="activeTab = 'mapa'">Mapa</button>
      <button v-if="isAdmin" class="tab" :class="{ active: activeTab === 'routes' }" @click="activeTab = 'routes'">Rutas</button>
      <button v-if="isAdmin" class="tab" :class="{ active: activeTab === 'zones' }" @click="activeTab = 'zones'">Zonas</button>
    </div>

    <div v-if="activeTab === 'mapa'" class="tab-content mapa-content">
      <div v-if="mapLoading" class="mapa-loading">Cargando mapa...</div>
      <div v-else-if="mapError" class="mapa-error">Error al cargar datos del mapa</div>
      <MapboxMap
        v-else
        :zones="mapData?.zones ?? []"
        :customers="mapData?.customers ?? []"
      />
    </div>

    <div v-if="activeTab === 'routes'" class="tab-content">
      <div class="toolbar">
        <Btn @click="handleCreateRoute">Nueva Ruta</Btn>
      </div>
      <DataTable :columns="columns" :rows="routes" @row-click="handleRowClick"/>
    </div>

    <div v-if="activeTab === 'zones'" class="tab-content">
      <div class="toolbar">
        <Btn @click="handleCreateZone">Nueva Zona</Btn>
      </div>
      <div v-if="!zones.length" class="empty-zone">No hay zonas creadas</div>
      <div v-else class="zone-grid">
        <GCard v-for="z in zones" :key="z.id" class="zone-card" @click="handleEditZone(z.id)">
          <div class="zone-card__header">
            <CardTitle>{{ z.name }}</CardTitle>
            <button class="zone-delete" @click.stop="handleDeleteZone(z.id)">
              <FontAwesomeIcon :icon="faTrash" />
            </button>
          </div>
          <div class="zone-card__body">
            <span class="zone-card__coords">{{ z.polygon?.length || 0 }} puntos</span>
          </div>
        </GCard>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.kpi-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; margin-bottom: 26px; }
.toolbar { display: flex; gap: 12px; margin-bottom: 18px; }

.tabs {
  display: flex;
  gap: 0;
  margin-bottom: 18px;
  border-bottom: 1px solid rgba(212,175,55,0.2);
}

.tab {
  padding: 10px 24px;
  background: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  color: #94a3b8;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.15s;
}

.tab:hover {
  color: #e0e0e0;
}

.tab.active {
  color: #d4af37;
  border-bottom-color: #d4af37;
}

.mapa-content {
  height: 600px;
}

.mapa-loading,
.mapa-error {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  min-height: 500px;
  background: #1a1a2e;
  border-radius: 8px;
  color: #94a3b8;
  font-size: 14px;
}

.zone-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 14px;
}

.zone-card {
  cursor: pointer;
  transition: transform 0.15s, box-shadow 0.15s;
}

.zone-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(212, 175, 55, 0.12);
}

.zone-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.zone-delete {
  background: transparent;
  border: none;
  color: #ef4444;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 14px;
  transition: background 0.15s;
}

.zone-delete:hover {
  background: rgba(239,68,68,0.15);
}

.zone-card__body {
  margin-top: 8px;
}

.zone-card__coords {
  font-size: 12px;
  color: #94a3b8;
}

.empty-zone {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
  font-size: 13px;
  border: 1px dashed rgba(212,175,55,0.2);
  border-radius: 8px;
}

@media screen and (max-width: 430px){
  .kpi-grid{
    grid-template-columns: 1fr;
  }
}
</style>
