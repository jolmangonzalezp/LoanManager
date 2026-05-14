export interface RouteDTO {
  id: string
  name: string
  zone_id: string
  user_ids: string[]
}

export interface RouteFormDTO {
  name: string
  zone_id: string
}

export interface ZoneDTO {
  id: string
  name: string
  polygon: number[][]
}

export interface MapDataDTO {
  zones: MapZoneDTO[]
  customers: MapCustomerDTO[]
}

export interface MapZoneDTO {
  id: string
  name: string
  polygon: number[][]
}

export interface MapCustomerDTO {
  id: string
  latitude: number
  longitude: number
  zone_id: string | null
  route_id: string | null
}
