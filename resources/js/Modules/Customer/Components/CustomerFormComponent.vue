<script setup lang="ts">
import {computed, onMounted, ref, nextTick, watch} from "vue";
import { useCustomer } from '@/Modules/Customer';
import { Btn, Select, Input, useModal, useNotifier } from '@/Shared';
import MapboxMap from '@/Modules/Geo/Components/MapboxMap.vue';
import LocationPicker from '@/Modules/Geo/Components/LocationPicker.vue';
import { RouteService } from '@/Modules/Route';
import mapboxgl from 'mapbox-gl';

interface Props {
  isEditing: boolean;
  id: string;
}

const props = defineProps<Props>();

const {customerForm, emptyCustomer, create, update} = useCustomer();
const { close } = useModal()
const notifier = useNotifier()

const docType = [
  {abrev:"CC", name:"Cédula de ciudadanía"},
  {abrev: "CE", name: "Cédula de extranjería"},
  {abrev: "NIT", name: "NIT"},
  {abrev: "PASSPORT", name: "Pasaporte"}
]

const options = computed(() =>
    docType.map(cn => ({
      label: `${cn.name}`,
      value: cn.abrev,
    }))
);

const mapComponentRef = ref<any>(null)
const locationPickerRef = ref<any>(null)
const zones = ref<any[]>([])
const showMap = ref(false)
const geoLoading = ref(false)
const pendingCoords = ref<[number, number] | null>(null)

const currentMap = computed(() => mapComponentRef.value?.mapInstance ?? null)

watch(currentMap, (m, oldM) => {
  if (!m) return

  if (oldM) {
    m.off('click')
  }

  m.on('click', (e: mapboxgl.MapMouseEvent) => {
    if (customerForm.value) {
      customerForm.value.longitude = e.lngLat.lng
      customerForm.value.latitude = e.lngLat.lat
    }
    placeMarkerAt(e.lngLat.lng, e.lngLat.lat)
    reverseGeocode(e.lngLat.lng, e.lngLat.lat)
  })

  if (pendingCoords.value) {
    const [lng, lat] = pendingCoords.value
    pendingCoords.value = null
    m.flyTo({ center: [lng, lat], zoom: 14, duration: 1000 })
    placeMarkerAt(lng, lat)
    if (customerForm.value) {
      customerForm.value.longitude = lng
      customerForm.value.latitude = lat
    }
    return
  }

  if (props.isEditing && customerForm.value?.latitude && customerForm.value?.longitude) {
    m.once('idle', () => {
      m.flyTo({ center: [customerForm.value!.longitude!, customerForm.value!.latitude!], zoom: 14, duration: 1000 })
      placeMarkerAt(customerForm.value!.longitude!, customerForm.value!.latitude!)
    })
  }
})

watch(showMap, (visible) => {
  if (visible && currentMap.value) {
    nextTick(() => currentMap.value.resize())
  }
})

async function reverseGeocode(lng: number, lat: number) {
  const token = import.meta.env.VITE_MAPBOX_TOKEN
  if (!token || !customerForm.value) return

  const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${token}&country=co&language=es&limit=1`

  try {
    const res = await fetch(url)
    const data = await res.json()

    if (data.features?.length) {
      customerForm.value.address = data.features[0].place_name
    }
  } catch {
    notifier.error('Error de geocodificación inversa', 'No se pudo obtener la dirección de la ubicación seleccionada.')
  }
}

function placeMarkerAt(lng: number, lat: number) {
  if (locationPickerRef.value) {
    locationPickerRef.value.clearLocation()
    locationPickerRef.value.placeMarker(lng, lat)
  }
}

async function searchAddress() {
  if (!customerForm.value?.address) return

  const token = import.meta.env.VITE_MAPBOX_TOKEN
  if (!token) return

  geoLoading.value = true
  showMap.value = true

  await nextTick()

  try {
    const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(customerForm.value.address)}.json?access_token=${token}&country=co&language=es&limit=1`
    const res = await fetch(url)
    const data = await res.json()

    if (data.features?.length) {
      const [lng, lat] = data.features[0].center

      if (customerForm.value) {
        customerForm.value.longitude = lng
        customerForm.value.latitude = lat
      }

      if (currentMap.value) {
        currentMap.value.flyTo({ center: [lng, lat], zoom: 14, duration: 1000 })
        placeMarkerAt(lng, lat)
      } else {
        pendingCoords.value = [lng, lat]
      }
    } else {
      notifier.error('Dirección no encontrada', 'No se encontraron resultados para la dirección ingresada.')
    }
  } catch {
    notifier.error('Error de geocodificación', 'Ocurrió un error al buscar la dirección en el mapa.')
  } finally {
    geoLoading.value = false
  }
}

async function save() {
  if (!customerForm.value) return
  if (props.isEditing) {
    const response = await update(props.id, customerForm.value)
      if (response) {
        close()
      }
  } else {
    const response = await create(customerForm.value);
      if (response) {
          close()
      }
  }
}

onMounted(async () => {
  if (!props.isEditing) {
    emptyCustomer();
  }
  try {
    const zoneList = await RouteService.getZones()
    zones.value = zoneList
  } catch {
    zones.value = []
  }
})

</script>

<template>
<div class="customer-form">
  <div class="customer-form__header">{{ props.isEditing === true ? "Actualizar Cliente" : "Crear Cliente"}}</div>
  <form v-if="customerForm" @submit.prevent="save" class="customer-form__form">
    <Select
      label="Tipo de documento:"
      :options="options"
      placeholder="Seleccione el tipo de documento"
      class="customer-form__input"
      v-model="customerForm.dni.type"
      :disabled="false"
    />
    <Input
        type="text"
        onlyNumbers
        label="Numero de documento:"
        placeholder="Ingrese el numero de documento"
        v-model="customerForm.dni.number"
        class="customer-form__input"
    />
    <Input
      type="text"
      label="Nombre:"
      placeholder="Ingrese el primer nombre"
      v-model="customerForm.name.firstName"
      class="customer-form__input"
    />
    <Input
        type="text"
        label="Segundo nombre (Opcional):"
        placeholder="Ingrese el segundo nombre"
        v-model="customerForm.name.middleName"
        class="customer-form__input"
    />
    <Input
        type="text"
        label="Apellido:"
        placeholder="Ingrese el apellido:"
        v-model="customerForm.name.lastName"
        class="customer-form__input"
    />
    <Input
        type="text"
        label="Segundo apellido:"
        placeholder="Ingrese el segundo apellido"
        v-model="customerForm.name.secondLastName"
        class="customer-form__input"
    />
    <Input
        type="text"
        label="Email:"
        placeholder="Ingrese el email"
        v-model="customerForm.email"
        class="customer-form__input"
    />

    <Input
        type="text"
        onlyNumbers
        label="Teléfono:"
        placeholder="Ingrese el numero de telefono"
        v-model="customerForm.phone"
        class="customer-form__input"
    />

    <div class="customer-form__address-row">
      <Input
          type="text"
          label="Dirección:"
          placeholder="Ingrese la direccion"
          v-model="customerForm.address"
          class="customer-form__address-input"
      />
      <Btn type="button" class="customer-form__map-btn" @click="searchAddress" :disabled="!customerForm.address || geoLoading">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
        {{ geoLoading ? 'Buscando...' : 'Mapa' }}
      </Btn>
    </div>

    <div v-show="showMap" class="customer-form__map-section">
      <div class="customer-form__map">
        <MapboxMap
          ref="mapComponentRef"
          :center="[-74.5, 4.6]"
          :zoom="5"
        />
      </div>
      <LocationPicker
        ref="locationPickerRef"
        :map="currentMap"
        :zones="zones"
        :alert-on-detect="true"
      />
      <div v-if="customerForm.latitude && customerForm.longitude" class="customer-form__coords">
        <span><strong>Lng:</strong> {{ customerForm.longitude.toFixed(6) }}</span>
        <span><strong>Lat:</strong> {{ customerForm.latitude.toFixed(6) }}</span>
      </div>
    </div>

    <div class="actions">
      <Btn>
        {{ props.isEditing === true ? "Actualizar" : "Crear"}}
      </Btn>
    </div>
  </form>
</div>
</template>

<style scoped>
.customer-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
  width: 100%;
  max-width: 600px;
}

.customer-form__header {
  width: 100%;
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  color: #d4af37;
}

.customer-form__form{
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 6px;
}

.customer-form__input{
  width: 280px;
  max-width: 400px;
}

.customer-form__address-row {
  display: flex;
  gap: 8px;
  align-items: flex-end;
  width: 100%;
}

.customer-form__address-input {
  flex: 1;
}

.customer-form__map-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
  height: 38px;
}

.customer-form__map-section {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 8px;
}

.customer-form__map {
  width: 100%;
  height: 300px;
  border-radius: 8px;
  overflow: hidden;
}

.customer-form__coords {
  display: flex;
  gap: 16px;
  padding: 6px 10px;
  background: #0f3460;
  border-radius: 6px;
  font-size: 0.813rem;
  color: #e0e0e0;
}

.actions{
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 1rem;
}
</style>
