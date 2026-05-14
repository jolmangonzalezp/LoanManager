export interface Route {
  id: string
  name: string
  zoneId: string
  zoneName: string
  userIds: string[]
  userCount: number
}

export interface RouteForm {
  name: string
  zoneId: string
}

export interface Zone {
  id: string
  name: string
  polygon: number[][]
}

export interface MapData {
  zones: MapZone[]
  customers: MapCustomer[]
}

export interface MapZone {
  id: string
  name: string
  polygon: number[][]
}

export interface MapCustomer {
  id: string
  latitude: number
  longitude: number
  zoneId: string | null
  routeId: string | null
}
