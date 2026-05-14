<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import mapboxgl from 'mapbox-gl'
import 'mapbox-gl/dist/mapbox-gl.css'

interface MapZone {
  id: string
  name: string
  polygon: number[][]
}

interface MapCustomer {
  id: string
  latitude: number
  longitude: number
}

const props = withDefaults(defineProps<{
  center?: [number, number]
  zoom?: number
  style?: string
  zones?: MapZone[]
  customers?: MapCustomer[]
  marker?: [number, number]
}>(), {
  zones: () => [],
  customers: () => [],
})

const mapContainer = ref<HTMLElement | null>(null)
const mapInstance = ref<mapboxgl.Map | null>(null)
const token = import.meta.env.VITE_MAPBOX_TOKEN as string | undefined
const missingToken = ref(!token)
const mapReady = ref(false)
let markerInstance: mapboxgl.Marker | null = null

function renderZones(map: mapboxgl.Map) {
  if (map.getLayer('zones-fill')) map.removeLayer('zones-fill')
  if (map.getLayer('zones-outline')) map.removeLayer('zones-outline')
  if (map.getSource('zones')) map.removeSource('zones')

  if (!props.zones.length) return

  const features = props.zones
    .filter(z => z.polygon?.length >= 3)
    .map(z => ({
      type: 'Feature' as const,
      properties: { id: z.id, name: z.name },
      geometry: {
        type: 'Polygon' as const,
        coordinates: [z.polygon],
      },
    }))

  if (!features.length) return

  map.addSource('zones', {
    type: 'geojson',
    data: { type: 'FeatureCollection', features },
  })

  map.addLayer({
    id: 'zones-fill',
    type: 'fill',
    source: 'zones',
    paint: {
      'fill-color': '#d4af37',
      'fill-opacity': 0.15,
    },
  })

  map.addLayer({
    id: 'zones-outline',
    type: 'line',
    source: 'zones',
    paint: {
      'line-color': '#d4af37',
      'line-width': 2,
    },
  })
}

function renderCustomers(map: mapboxgl.Map) {
  if (map.getLayer('customers-circle')) map.removeLayer('customers-circle')
  if (map.getSource('customers')) map.removeSource('customers')

  if (!props.customers.length) return

  const features = props.customers
    .filter(c => c.latitude && c.longitude)
    .map(c => ({
      type: 'Feature' as const,
      properties: { id: c.id },
      geometry: {
        type: 'Point' as const,
        coordinates: [c.longitude, c.latitude],
      },
    }))

  if (!features.length) return

  map.addSource('customers', {
    type: 'geojson',
    data: { type: 'FeatureCollection', features },
  })

  map.addLayer({
    id: 'customers-circle',
    type: 'circle',
    source: 'customers',
    paint: {
      'circle-radius': 6,
      'circle-color': '#3b82f6',
      'circle-opacity': 0.8,
      'circle-stroke-width': 2,
      'circle-stroke-color': '#ffffff',
    },
  })
}

function renderMapData(map: mapboxgl.Map) {
  renderZones(map)
  renderCustomers(map)
}

function renderMarker(map: mapboxgl.Map) {
  if (markerInstance) {
    markerInstance.remove()
    markerInstance = null
  }

  if (!props.marker) return

  const el = document.createElement('div')
  el.className = 'mapbox-marker'
  el.innerHTML = `
    <svg width="32" height="32" viewBox="0 0 24 24" fill="#d4af37" stroke="#16213e" stroke-width="1.5">
      <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
      <circle cx="12" cy="9" r="2.5" fill="white" stroke="none"/>
    </svg>`

  markerInstance = new mapboxgl.Marker({ element: el, anchor: 'bottom' })
    .setLngLat(props.marker)
    .addTo(map)
}

function fitBounds(map: mapboxgl.Map) {
  const allCoords: [number, number][] = []

  for (const z of props.zones) {
    if (z.polygon?.length) {
      for (const coord of z.polygon) {
        if (coord.length >= 2) allCoords.push([coord[0], coord[1]])
      }
    }
  }

  for (const c of props.customers) {
    if (c.longitude && c.latitude) {
      allCoords.push([c.longitude, c.latitude])
    }
  }

  if (props.marker) {
    allCoords.push(props.marker)
  }

  if (allCoords.length > 1) {
    const bounds = allCoords.reduce((b, coord) => b.extend(coord as [number, number]), new mapboxgl.LngLatBounds(allCoords[0], allCoords[0]))
    map.fitBounds(bounds, { padding: 50, maxZoom: 14 })
  }
}

watch([() => props.zones, () => props.customers], () => {
  if (mapInstance.value && mapReady.value) {
    renderMapData(mapInstance.value)
    fitBounds(mapInstance.value)
  }
}, { deep: true })

onMounted(() => {
  if (!token || !mapContainer.value) return

  mapboxgl.accessToken = token

  const map = new mapboxgl.Map({
    container: mapContainer.value,
    style: props.style || 'mapbox://styles/mapbox/streets-v12',
    center: props.center || [-74.5, 4.6],
    zoom: props.zoom || 5,
  })

  map.addControl(new mapboxgl.NavigationControl(), 'top-right')

  map.on('load', () => {
    mapInstance.value = map
    mapReady.value = true
    renderMapData(map)
    renderMarker(map)
    if (props.zones.length || props.customers.length) {
      fitBounds(map)
    }
  })
})

watch(() => props.marker, () => {
  if (mapInstance.value && mapReady.value) {
    renderMarker(mapInstance.value)
  }
})

defineExpose({ mapInstance })
</script>

<template>
  <div v-if="missingToken" class="mapbox-missing-token">
    <div class="mapbox-missing-token__card">
      <h2>Mapbox Token requerido</h2>
      <p>Para usar este demo necesitas un token de Mapbox.</p>
      <ol>
        <li>Regístrate en <a href="https://account.mapbox.com" target="_blank">account.mapbox.com</a></li>
        <li>Copia tu token público (pk.eyJ...)</li>
        <li>Agrégalo al archivo <code>.env</code>:</li>
      </ol>
      <pre>VITE_MAPBOX_TOKEN=pk.eyJ...tu-token-aqui</pre>
      <p>Luego reinicia el servidor de desarrollo.</p>
    </div>
  </div>
  <div v-else ref="mapContainer" class="mapbox-map"></div>
</template>

<style scoped>
.mapbox-map {
  width: 100%;
  height: 100%;
  border-radius: 8px;
}

:deep(.mapbox-marker) {
  cursor: default;
}

.mapbox-missing-token {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  min-height: 500px;
  background: #1a1a2e;
  border-radius: 8px;
}

.mapbox-missing-token__card {
  background: #16213e;
  padding: 2rem;
  border-radius: 8px;
  color: #e0e0e0;
  max-width: 480px;
}

.mapbox-missing-token__card h2 {
  color: #d4af37;
  margin-bottom: 1rem;
}

.mapbox-missing-token__card ol {
  margin: 1rem 0;
  padding-left: 1.5rem;
}

.mapbox-missing-token__card code {
  background: #0f3460;
  padding: 0.125rem 0.375rem;
  border-radius: 4px;
}

.mapbox-missing-token__card pre {
  background: #0f3460;
  padding: 0.75rem;
  border-radius: 4px;
  overflow-x: auto;
  font-size: 0.875rem;
}

.mapbox-missing-token__card a {
  color: #d4af37;
}
</style>
