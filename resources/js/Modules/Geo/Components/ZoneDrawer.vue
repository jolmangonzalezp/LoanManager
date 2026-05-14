<script setup lang="ts">
import { ref, watch } from 'vue'
import MapboxDraw from '@mapbox/mapbox-gl-draw'
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css'

const props = defineProps<{
  map: any
  zones: any[]
}>()

const emit = defineEmits<{
  (e: 'update:zones', zones: any[]): void
}>()

const drawInstance = ref<MapboxDraw | null>(null)
const drawingMode = ref(false)

function initDraw() {
  if (!props.map || drawInstance.value) return

  const draw = new MapboxDraw({
    displayControlsDefault: false,
    controls: {},
    defaultMode: 'simple_select',
  })

  props.map.addControl(draw, 'top-left')
  drawInstance.value = draw

  props.map.on('draw.create', syncZones)
  props.map.on('draw.update', syncZones)
  props.map.on('draw.delete', syncZones)
}

function syncZones() {
  if (!drawInstance.value) return
  emit('update:zones', drawInstance.value.getAll().features)
}

function toggleDrawing() {
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
  syncZones()
}

function loadFromLocal() {
  if (!drawInstance.value) return
  const saved = localStorage.getItem('geo_zones')
  if (saved) {
    try { drawInstance.value.set(JSON.parse(saved)); syncZones() } catch { /* ignore */ }
  }
}

function saveToLocal() {
  localStorage.setItem('geo_zones', JSON.stringify({ type: 'FeatureCollection', features: props.zones }))
}

function exportZones() {
  if (!props.zones.length) return
  const blob = new Blob([JSON.stringify({ type: 'FeatureCollection', features: props.zones }, null, 2)], { type: 'application/json' })
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

function clearAllZones() {
  localStorage.removeItem('geo_zones')
  drawInstance.value?.deleteAll()
  syncZones()
}

watch(() => props.map, (m) => {
  if (m) {
    setTimeout(() => { initDraw(); loadFromLocal() }, 600)
  }
})
</script>

<template>
  <div class="zd-toolbar">
    <button class="zd-btn" :class="{ active: drawingMode }" @click="toggleDrawing">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
      </svg>
      {{ drawingMode ? 'Terminar' : 'Dibujar zona' }}
    </button>
    <button class="zd-btn" @click="deleteSelected">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
      </svg>
    </button>
    <button class="zd-btn" @click="clearAll">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10" /><line x1="15" y1="9" x2="9" y2="15" /><line x1="9" y1="9" x2="15" y2="15" />
      </svg>
    </button>
  </div>

  <div class="zd-legend">
    <span class="zd-legend__badge">{{ props.zones.length }} zona{{ props.zones.length !== 1 ? 's' : '' }}</span>
  </div>

  <div class="zd-actions">
    <button class="zd-btn zd-btn--sm" @click="exportZones" :disabled="!props.zones.length">Exportar</button>
    <button class="zd-btn zd-btn--sm" @click="importZones">Importar</button>
    <button class="zd-btn zd-btn--sm" @click="saveToLocal" :disabled="!props.zones.length">Guardar</button>
    <button class="zd-btn zd-btn--sm zd-btn--danger" @click="clearAllZones">Limpiar</button>
  </div>
</template>

<style scoped>
.zd-toolbar { display: flex; gap: 4px; flex-wrap: wrap; }
.zd-btn {
  display: flex; align-items: center; gap: 4px; padding: 6px 12px;
  background: #0f3460; border: 1px solid #1a1a5e; border-radius: 6px;
  color: #e0e0e0; cursor: pointer; font-size: 0.813rem; transition: all 0.15s;
}
.zd-btn:hover { background: #1a1a5e; }
.zd-btn.active { background: #d4af37; color: #16213e; }
.zd-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.zd-btn--sm { padding: 4px 10px; font-size: 0.75rem; }
.zd-btn--danger { border-color: #5e1a1a; color: #ff6b6b; }
.zd-btn--danger:hover { background: #3a1a1a; }
.zd-legend { margin-top: 6px; }
.zd-legend__badge {
  display: inline-block; padding: 2px 10px; background: #0f3460;
  border-radius: 12px; font-size: 0.75rem; color: #d4af37;
}
.zd-actions { display: flex; gap: 4px; margin-top: 8px; flex-wrap: wrap; }
</style>
