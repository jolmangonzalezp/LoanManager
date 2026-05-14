<script setup lang="ts">
import { ref } from 'vue'
import mapboxgl from 'mapbox-gl'
import booleanPointInPolygon from '@turf/boolean-point-in-polygon'
import { point, polygon } from '@turf/helpers'
import { useNotifier } from '@/Shared'

const props = withDefaults(defineProps<{
  map: any
  zones: any[]
  alertOnDetect?: boolean
}>(), {
  alertOnDetect: false,
})

const selectedLocation = ref<{ lng: number; lat: number } | null>(null)
const detectedZone = ref<string | null>(null)
const detectedZoneIndex = ref<number | null>(null)
const locationName = ref<string>('')
const gpsLoading = ref(false)
const hasMarker = ref(false)
let marker: mapboxgl.Marker | null = null

function detectZone(lng: number, lat: number) {
  detectedZone.value = null
  detectedZoneIndex.value = null

  if (!props.zones.length) return

  const pt = point([lng, lat])

  for (let i = 0; i < props.zones.length; i++) {
    const zone = props.zones[i]
    let geom: any = null

    if (zone.geometry?.type === 'Polygon') {
      const ring = zone.geometry.coordinates[0]
      const first = ring[0]
      const last = ring[ring.length - 1]
      if (first[0] !== last[0] || first[1] !== last[1]) {
        zone.geometry.coordinates[0] = [...ring, [first[0], first[1]]]
      }
      geom = zone.geometry
    } else if (zone.geometry?.type === 'MultiPolygon') {
      for (const poly of zone.geometry.coordinates) {
        const ring = poly[0]
        const first = ring[0]
        const last = ring[ring.length - 1]
        if (first[0] !== last[0] || first[1] !== last[1]) {
          poly[0] = [...ring, [first[0], first[1]]]
        }
      }
      geom = zone.geometry
    } else if (zone.polygon?.length >= 3) {
      const ring = [...zone.polygon]
      const first = ring[0]
      const last = ring[ring.length - 1]
      if (first[0] !== last[0] || first[1] !== last[1]) {
        ring.push([first[0], first[1]])
      }
      geom = polygon([ring]).geometry
    }

    if (!geom) continue

    try {
      if (booleanPointInPolygon(pt, geom)) {
        detectedZone.value = zone.properties?.name || zone.properties?.id || zone.name || `Zona ${i + 1}`
        detectedZoneIndex.value = i
        return
      }
    } catch { /* ignore */ }
  }
}

function placeMarker(lng: number, lat: number) {
  if (!props.map) return

  if (marker) marker.remove()

  const el = document.createElement('div')
  el.className = 'location-marker'
  el.innerHTML = `
    <svg width="32" height="32" viewBox="0 0 24 24" fill="#d4af37" stroke="#16213e" stroke-width="1.5">
      <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
      <circle cx="12" cy="9" r="2.5" fill="white" stroke="none"/>
    </svg>`

  marker = new mapboxgl.Marker({ element: el, anchor: 'bottom' })
    .setLngLat([lng, lat])
    .addTo(props.map)

  props.map.flyTo({ center: [lng, lat], zoom: 14, duration: 1000 })

  selectedLocation.value = { lng, lat }
  hasMarker.value = true
  detectZone(lng, lat)
  if (props.alertOnDetect && detectedZone.value) {
    useNotifier().info('Zona detectada', detectedZone.value)
  }
}

let gpsInProgress = false

function useGPS() {
  if (gpsInProgress) return
  if (!navigator.geolocation) {
    locationName.value = 'GPS no disponible en este navegador'
    return
  }

  gpsInProgress = true
  gpsLoading.value = true
  locationName.value = 'Obteniendo ubicación...'

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const { latitude, longitude } = pos.coords
      placeMarker(longitude, latitude)
      locationName.value = 'Ubicación GPS obtenida'
      gpsLoading.value = false
      gpsInProgress = false
    },
    (err) => {
      locationName.value = `Error GPS: ${err.message}`
      gpsLoading.value = false
      gpsInProgress = false
    },
    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
  )
}

function clearLocation() {
  if (marker) marker.remove()
  marker = null
  selectedLocation.value = null
  detectedZone.value = null
  detectedZoneIndex.value = null
  locationName.value = ''
  hasMarker.value = false
}

defineExpose({
  clearLocation,
  placeMarker,
  selectedLocation,
  detectedZone,
  detectedZoneIndex,
  hasMarker,
})
</script>

<template>
  <div class="location-picker">
    <div class="location-picker__buttons">
      <button
        class="location-picker-btn"
        @click="useGPS"
        :disabled="gpsLoading"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2a10 10 0 1 0 10 10h-4a6 6 0 1 1-6-6V2z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
        {{ gpsLoading ? 'Obteniendo...' : 'Usar GPS' }}
      </button>

      <span class="location-picker__hint">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        Haz clic en el mapa
      </span>

      <button
        v-if="hasMarker"
        class="location-picker-btn"
        @click="clearLocation"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
        Limpiar
      </button>
    </div>

    <div class="location-picker__results">
      <div v-if="selectedLocation" class="location-picker__coords">
        <span><strong>Lng:</strong> {{ selectedLocation.lng.toFixed(6) }}</span>
        <span><strong>Lat:</strong> {{ selectedLocation.lat.toFixed(6) }}</span>
      </div>

      <div v-if="detectedZone" class="location-picker__zone">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d4af37" stroke-width="2">
          <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
        </svg>
        Zona detectada: <strong>{{ detectedZone }}</strong>
      </div>

      <div v-if="selectedLocation && !detectedZone && zones.length > 0" class="location-picker__nozone">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff6b6b" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        No está en una zona creada
      </div>

      <div v-if="locationName" class="location-picker__gps-msg">{{ locationName }}</div>
    </div>
  </div>
</template>

<style scoped>
.location-picker {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.location-picker__buttons {
  display: flex;
  gap: 6px;
  align-items: center;
  flex-wrap: wrap;
}

.location-picker-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  background: #0f3460;
  border: 1px solid #1a1a5e;
  border-radius: 6px;
  color: #e0e0e0;
  cursor: pointer;
  font-size: 0.813rem;
  transition: all 0.15s;
}

.location-picker-btn:hover:not(:disabled) {
  background: #1a1a5e;
}

.location-picker-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.location-picker__hint {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.75rem;
  color: #888;
}

.location-picker__results {
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: 0.813rem;
}

.location-picker__coords {
  display: flex;
  gap: 16px;
  padding: 4px 0;
  color: #e0e0e0;
}

.location-picker__zone {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 10px;
  background: #1a3a1a;
  border-radius: 6px;
  color: #90ee90;
}

.location-picker__nozone {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 10px;
  background: #3a1a1a;
  border-radius: 6px;
  color: #ff6b6b;
}

.location-picker__gps-msg {
  font-size: 0.75rem;
  color: #888;
}

:deep(.location-marker) {
  cursor: pointer;
}
</style>
