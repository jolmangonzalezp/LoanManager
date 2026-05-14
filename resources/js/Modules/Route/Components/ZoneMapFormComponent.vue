<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'
import mapboxgl from 'mapbox-gl'
import 'mapbox-gl/dist/mapbox-gl.css'
import MapboxDraw from '@mapbox/mapbox-gl-draw'
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css'
import { useRoute } from '@/Modules/Route'
import { Btn, Input, useModal } from '@/Shared'

interface Props {
  id?: string
}

const props = defineProps<Props>()

const { createZone, updateZone, zoneForm, fillZone } = useRoute()
const { close } = useModal()

const isEditing = !!props.id

const zoneName = ref('')
const polygonText = ref('')
const showMap = ref(false)

const mapContainer = ref<HTMLElement | null>(null)
const map = ref<any>(null)
const drawInstance = ref<any>(null)
const token = import.meta.env.VITE_MAPBOX_TOKEN as string | undefined
const missingToken = ref(!token)
const mapReady = ref(false)

function onFieldFocus() {
  if (missingToken.value) return
  showMap.value = true
  nextTick(() => {
    setTimeout(() => initMap(), 250)
  })
}

function initMap() {
  if (!token || !mapContainer.value || map.value) return

  mapboxgl.accessToken = token

  const m = new mapboxgl.Map({
    container: mapContainer.value,
    style: 'mapbox://styles/mapbox/streets-v12',
    center: [-74.1990, 11.2408],
    zoom: 12,
  })

  m.addControl(new mapboxgl.NavigationControl(), 'top-right')

  m.on('load', () => {
    const draw = new MapboxDraw({
      displayControlsDefault: false,
      controls: {},
      defaultMode: 'simple_select',
    })
    m.addControl(draw, 'top-left')
    drawInstance.value = draw

    map.value = m
    mapReady.value = true
    m.resize()

    // If editing, draw existing polygon on the map
    if (polygonText.value) {
      try {
        const coords = JSON.parse(polygonText.value)
        if (Array.isArray(coords) && coords.length >= 3) {
          // Close the polygon ring if needed
          const ring = coords.map((c: number[]) => [c[0], c[1]])
          const first = ring[0]
          const last = ring[ring.length - 1]
          if (first[0] !== last[0] || first[1] !== last[1]) {
            ring.push([first[0], first[1]])
          }
          draw.add({
            type: 'Feature',
            geometry: {
              type: 'Polygon',
              coordinates: [ring],
            },
          })
          // Fit map to the polygon bounds
          const bounds = new mapboxgl.LngLatBounds()
          coords.forEach((c: number[]) => bounds.extend(c as [number, number]))
          m.fitBounds(bounds, { padding: 60 })
          return
        }
      } catch {
        // Invalid polygon data, fall through to draw mode
      }
    }
    draw.changeMode('draw_polygon')
  })
}

function syncCoords() {
  if (!drawInstance.value) return
  const features = drawInstance.value.getAll().features
  if (features.length > 0) {
    const coords = features[0].geometry.coordinates[0]
    const cleaned = coords.slice(0, -1).map((c: number[]) => [c[0], c[1]])
    polygonText.value = JSON.stringify(cleaned)
  }
}

function confirmMap() {
  syncCoords()
  closeMap()
}

function closeMap() {
  showMap.value = false
  map.value = null
  drawInstance.value = null
  mapReady.value = false
  nextTick(() => {
    const container = mapContainer.value
    if (container) container.innerHTML = ''
  })
}

function deleteSelected() {
  if (!drawInstance.value) return
  drawInstance.value.getSelectedIds().forEach((id: string) => drawInstance.value!.delete(id))
}

function clearPolygon() {
  drawInstance.value?.deleteAll()
}

async function save() {
  if (!zoneName.value.trim()) {
    alert('Ingrese un nombre para la zona')
    return
  }
  let polygon: number[][]
  try {
    polygon = JSON.parse(polygonText.value)
    if (!Array.isArray(polygon) || polygon.length < 3) {
      throw new Error('Debe tener al menos 3 coordenadas')
    }
  } catch (e: any) {
    alert('Polygon inválido: ' + e.message)
    return
  }
  if (isEditing && props.id) {
    const response = await updateZone(props.id, zoneName.value.trim(), polygon)
    if (response) close()
  } else {
    const response = await createZone(zoneName.value.trim(), polygon)
    if (response) close()
  }
}

function handlePaste(e: ClipboardEvent) {
  const text = e.clipboardData?.getData('text') || ''
  if (text.startsWith('[[')) {
    polygonText.value = text
    e.preventDefault()
  }
}

onMounted(() => {
  if (isEditing && props.id) {
    fillZone(props.id)
    if (zoneForm.value) {
      zoneName.value = zoneForm.value.name
      polygonText.value = zoneForm.value.polygon
    }
  }
})
</script>

<template>
<div class="zone-form">
  <div class="zone-form__header">{{ isEditing ? 'Actualizar Zona' : 'Crear Zona' }}</div>

  <div v-if="missingToken" class="zone-form__no-token">
    <p>Se requiere un token de Mapbox. Agrega <code>VITE_MAPBOX_TOKEN</code> en tu <code>.env</code>.</p>
  </div>

  <template v-else>
    <Input
      type="text"
      label="Nombre de la zona:"
      placeholder="Ej: Zona Centro"
      v-model="zoneName"
    />

    <div class="zone-form__field">
      <label class="zone-form__label">Coordenadas (polígono):</label>
      <textarea
        v-model="polygonText"
        class="zone-form__textarea"
        placeholder="Dibuja en el mapa o pega coordenadas aquí"
        rows="4"
        @focus="onFieldFocus"
        @paste="handlePaste"
      ></textarea>
      <span class="zone-form__hint">Haz clic para dibujar el polígono en el mapa. Formato: [[lng, lat], ...]</span>
    </div>

    <div class="zone-form__actions">
      <Btn variant="secondary" @click="close">Cancelar</Btn>
      <Btn @click="save" :disabled="!polygonText.trim() || !zoneName.trim()">{{ isEditing ? 'Actualizar' : 'Guardar Zona' }}</Btn>
    </div>
  </template>

  <Teleport to="body">
    <div v-if="showMap" class="map-overlay" @click.self="closeMap">
      <div class="map-overlay__inner">
        <div class="map-overlay__header">
          <h2>Dibujar polígono de zona</h2>
          <div class="map-overlay__tools">
            <button class="mo-btn" @click="deleteSelected">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
              </svg>
              Eliminar
            </button>
            <button class="mo-btn" @click="clearPolygon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" /><line x1="15" y1="9" x2="9" y2="15" /><line x1="9" y1="9" x2="15" y2="15" />
              </svg>
              Limpiar
            </button>
          </div>
          <div class="map-overlay__actions">
            <button class="mo-btn mo-btn--cancel" @click="closeMap">Cancelar</button>
            <button class="mo-btn mo-btn--confirm" @click="confirmMap">Confirmar</button>
          </div>
        </div>
        <div class="map-overlay__map" ref="mapContainer">
          <div v-if="!mapReady" class="map-overlay__loading">Cargando mapa...</div>
        </div>
      </div>
    </div>
  </Teleport>
</div>
</template>

<style scoped>
.zone-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
  width: 100%;
  max-width: 400px;
}

.zone-form__header {
  width: 100%;
  text-align: center;
  font-size: 1.5rem;
  font-weight: 700;
  color: #d4af37;
}

.zone-form__no-token {
  text-align: center;
  padding: 20px;
  color: #94a3b8;
}

.zone-form__no-token code {
  background: #0f3460;
  padding: 2px 6px;
  border-radius: 4px;
  color: #d4af37;
}

.zone-form__field {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.zone-form__label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #d4af37;
}

.zone-form__textarea {
  width: 100%;
  padding: 10px 12px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(212,175,55,0.28);
  border-radius: 8px;
  color: #e0e0e0;
  font-family: 'Share Tech Mono', monospace;
  font-size: 13px;
  resize: vertical;
  box-sizing: border-box;
  cursor: pointer;
}

.zone-form__textarea:focus {
  outline: none;
  border-color: #d4af37;
  box-shadow: 0 0 0 2px rgba(212,175,55,0.15);
}

.zone-form__hint {
  font-size: 11px;
  color: #94a3b8;
}

.zone-form__actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-top: 4px;
}

/* Map Fullscreen Overlay */
.map-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  background: rgba(0,0,0,0.85);
  display: flex;
  justify-content: center;
  align-items: center;
  backdrop-filter: blur(4px);
}

.map-overlay__inner {
  width: 95vw;
  height: 95vh;
  display: flex;
  flex-direction: column;
  background: #0a1f1a;
  border: 1px solid rgba(212,175,55,0.28);
  border-radius: 12px;
  overflow: hidden;
}

.map-overlay__header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  background: #16213e;
  border-bottom: 1px solid #0f3460;
  flex-wrap: wrap;
}

.map-overlay__header h2 {
  font-size: 1rem;
  color: #d4af37;
  margin: 0;
  white-space: nowrap;
}

.map-overlay__tools {
  display: flex;
  gap: 4px;
  flex: 1;
}

.map-overlay__actions {
  display: flex;
  gap: 6px;
}

.map-overlay__map {
  flex: 1;
  position: relative;
  min-height: 0;
}

.map-overlay__loading {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #94a3b8;
}

.mo-btn {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  background: #0f3460;
  border: 1px solid #1a1a5e;
  border-radius: 6px;
  color: #e0e0e0;
  cursor: pointer;
  font-size: 0.813rem;
  transition: all 0.15s;
  white-space: nowrap;
}

.mo-btn:hover {
  background: #1a1a5e;
}

.mo-btn--cancel {
  border-color: #5e1a1a;
  color: #ff6b6b;
}

.mo-btn--cancel:hover {
  background: #3a1a1a;
}

.mo-btn--confirm {
  background: #d4af37;
  color: #16213e;
  font-weight: 700;
}

.mo-btn--confirm:hover {
  background: #e8c84a;
}
</style>
