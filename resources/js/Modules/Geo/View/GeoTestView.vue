<script setup lang="ts">
import { ref } from 'vue'
import MapboxMap from '@/Modules/Geo/Components/MapboxMap.vue'
import LocationPicker from '@/Modules/Geo/Components/LocationPicker.vue'
import MapboxDraw from '@mapbox/mapbox-gl-draw'
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css'

const map = ref<any>(null)
const zones = ref<any[]>([])
const activeTab = ref<'draw' | 'pick'>('draw')
const locationPickerRef = ref<InstanceType<typeof LocationPicker> | null>(null)
const gpsStatus = ref<'idle' | 'searching' | 'done' | 'unavailable'>('idle')
const drawInstance = ref<any>(null)
const drawingMode = ref(false)

const SANTA_MARTA: [number, number, number] = [11.2408, -74.1990, 13]

function centerOnSantaMarta(m: any) {
  console.log('[GeoTest] Centrando en Santa Marta')
  m.flyTo({ center: [SANTA_MARTA[1], SANTA_MARTA[0]], zoom: SANTA_MARTA[2], duration: 1500, pitch: 45 })
  gpsStatus.value = 'unavailable'
}

function onMapLoaded(m: any) {
  map.value = m
  gpsStatus.value = 'searching'

  m.once('idle', () => {
    const draw = new MapboxDraw({
      displayControlsDefault: false,
      controls: { polygon: true, trash: true },
      defaultMode: 'simple_select',
    })
    m.addControl(draw, 'top-left')
    drawInstance.value = draw

    m.on('draw.create', syncZones)
    m.on('draw.update', syncZones)
    m.on('draw.delete', syncZones)

    const saved = localStorage.getItem('geo_zones')
    if (saved) {
      try {
        draw.set(JSON.parse(saved))
        syncZones()
      } catch {}
    }

    setTimeout(() => {
      if (!navigator.geolocation) {
        centerOnSantaMarta(m)
        return
      }
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const { latitude, longitude } = pos.coords
          m.flyTo({ center: [longitude, latitude], zoom: 14, duration: 1500 })
          gpsStatus.value = 'done'
        },
        () => centerOnSantaMarta(m),
        { enableHighAccuracy: false, timeout: 12000, maximumAge: 120000 }
      )
    }, 500)
  })
}

function syncZones() {
  if (!drawInstance.value) return
  zones.value = drawInstance.value.getAll().features
}

function toggleDraw() {
  if (!drawInstance.value) return
  drawingMode.value = !drawingMode.value
  drawInstance.value.changeMode(drawingMode.value ? 'draw_polygon' : 'simple_select')
}

function deleteSelected() {
  if (!drawInstance.value) return
  drawInstance.value.getSelectedIds().forEach((id: string) => drawInstance.value!.delete(id))
  syncZones()
}

function clearAll() {
  drawInstance.value?.deleteAll()
  zones.value = []
}

function saveZones() {
  if (!zones.value.length) return
  localStorage.setItem('geo_zones', JSON.stringify({ type: 'FeatureCollection', features: zones.value }))
}

function exportZones() {
  if (!zones.value.length) return
  const blob = new Blob([JSON.stringify({ type: 'FeatureCollection', features: zones.value }, null, 2)], { type: 'application/json' })
  const a = document.createElement('a')
  a.href = URL.createObjectURL(blob)
  a.download = 'zonas.json'
  a.click()
}

function importZones() {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = '.json'
  input.onchange = (e) => {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (!file) return
    const reader = new FileReader()
    reader.onload = () => {
      try {
        const data = JSON.parse(reader.result as string)
        if (data.type === 'FeatureCollection' && Array.isArray(data.features)) {
          localStorage.setItem('geo_zones', JSON.stringify(data))
          alert('Zonas importadas. Recarga la página.')
        }
      } catch { alert('Archivo inválido') }
    }
    reader.readAsText(file)
  }
  input.click()
}

function switchTab(tab: 'draw' | 'pick') {
  if (locationPickerRef.value) {
    locationPickerRef.value.clearLocation()
  }
  activeTab.value = tab
}
</script>

<template>
  <div class="geo-test">
    <header class="geo-test__header">
      <h1>Demo de Geolocalización</h1>
      <span class="geo-test__version">POC</span>
    </header>

    <div class="geo-test__tabs">
      <button class="geo-tab" :class="{ active: activeTab === 'draw' }" @click="switchTab('draw')">
        Dibujar zonas
      </button>
      <button class="geo-tab" :class="{ active: activeTab === 'pick' }" @click="switchTab('pick')">
        Probar ubicación
      </button>
    </div>

    <div class="geo-test__content">
      <div class="geo-test__sidebar">
        <div v-if="activeTab === 'draw'" class="geo-test__panel">
          <h3>Configuración de Zonas</h3>
          <p class="geo-test__desc">Dibuja polígonos en el mapa. Usa los controles en el mapa para crear polígonos.</p>

          <div class="zd-actions">
            <button class="zd-btn" :class="{ active: drawingMode }" @click="toggleDraw">
              {{ drawingMode ? 'Terminar' : 'Dibujar' }}
            </button>
            <button class="zd-btn" @click="deleteSelected">Eliminar</button>
            <button class="zd-btn" @click="clearAll">Limpiar</button>
            <button class="zd-btn zd-btn--sm" @click="exportZones" :disabled="!zones.length">Exportar</button>
            <button class="zd-btn zd-btn--sm" @click="importZones">Importar</button>
            <button class="zd-btn zd-btn--sm" @click="saveZones" :disabled="!zones.length">Guardar</button>
          </div>

          <div v-if="zones.length" style="margin-top:8px">
            <span class="zd-badge">{{ zones.length }} zona{{ zones.length !== 1 ? 's' : '' }}</span>
          </div>
        </div>

        <div v-if="activeTab === 'pick'" class="geo-test__panel">
          <h3>Detección de Zona</h3>
          <p class="geo-test__desc">Usa el GPS o haz clic en el mapa para detectar a qué zona pertenece la ubicación.</p>
          <LocationPicker ref="locationPickerRef" :map="map" :zones="zones" />
        </div>
      </div>

      <div class="geo-test__map">
        <MapboxMap :center="[-74.1990, 11.2408]" :zoom="12" @map-loaded="onMapLoaded" />
      </div>
    </div>

    <div class="geo-test__footer">
      <span v-if="zones.length">{{ zones.length }} zona{{ zones.length !== 1 ? 's' : '' }}</span>
      <span v-else>Sin zonas</span>
      <span class="geo-test__gps" :class="gpsStatus">
        {{ gpsStatus === 'searching' ? 'Buscando...' : gpsStatus === 'done' ? 'Ubicación GPS' : 'GPS no disponible' }}
      </span>
    </div>
  </div>
</template>

<style scoped>
.geo-test {
  display: flex; flex-direction: column; height: 100vh;
  background: #1a1a2e; color: #e0e0e0; font-family: system-ui, -apple-system, sans-serif;
}
.geo-test__header {
  display: flex; align-items: center; gap: 12px; padding: 12px 24px;
  background: #16213e; border-bottom: 1px solid #0f3460;
}
.geo-test__header h1 { font-size: 1.25rem; font-weight: 700; color: #d4af37; margin: 0; }
.geo-test__version {
  font-size: 0.688rem; background: #d4af37; color: #16213e;
  padding: 2px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase;
}
.geo-test__tabs { display: flex; background: #16213e; border-bottom: 1px solid #0f3460; }
.geo-tab {
  padding: 10px 20px; background: transparent; border: none;
  border-bottom: 2px solid transparent; color: #888; cursor: pointer; font-size: 0.875rem;
}
.geo-tab:hover { color: #e0e0e0; }
.geo-tab.active { color: #d4af37; border-bottom-color: #d4af37; }
.geo-test__content { display: flex; flex: 1; overflow: hidden; }
.geo-test__sidebar {
  width: 320px; min-width: 320px; padding: 16px;
  background: #16213e; overflow-y: auto; border-right: 1px solid #0f3460;
}
.geo-test__panel h3 { font-size: 1rem; color: #d4af37; margin: 0 0 8px; }
.geo-test__desc { font-size: 0.813rem; color: #888; margin: 0 0 12px; line-height: 1.5; }
.geo-test__map { flex: 1; position: relative; }
.geo-test__footer {
  display: flex; align-items: center; gap: 12px; padding: 8px 24px;
  background: #16213e; border-top: 1px solid #0f3460; font-size: 0.75rem; color: #888;
}
.geo-test__gps { margin-left: auto; }
.geo-test__gps.searching { color: #FFE66D; animation: pulse 1.5s infinite; }
.geo-test__gps.done { color: #4ECDC4; }
.geo-test__gps.unavailable { color: #888; }
@keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
.zd-actions { display: flex; gap: 4px; flex-wrap: wrap; }
.zd-btn {
  padding: 6px 12px; background: #0f3460; border: 1px solid #1a1a5e;
  border-radius: 6px; color: #e0e0e0; cursor: pointer; font-size: 0.813rem;
}
.zd-btn:hover { background: #1a1a5e; }
.zd-btn.active { background: #d4af37; color: #16213e; }
.zd-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.zd-btn--sm { padding: 4px 10px; font-size: 0.75rem; }
.zd-badge {
  display: inline-block; padding: 2px 10px; background: #0f3460;
  border-radius: 12px; font-size: 0.75rem; color: #d4af37;
}
</style>
