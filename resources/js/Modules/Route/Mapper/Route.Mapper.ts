import type { Route, RouteForm, Zone, MapData, MapZone, MapCustomer } from '@/Modules/Route';
import type { RouteDTO, RouteFormDTO, ZoneDTO, MapDataDTO } from '@/Modules/Route';

export const RouteMapper = {

  toDomain(dto: RouteDTO): Route {
    return {
      id: dto.id,
      name: dto.name,
      zoneId: dto.zone_id,
      zoneName: '',
      userIds: dto.user_ids || [],
      userCount: (dto.user_ids || []).length,
    }
  },

  toDomainInList(dtos: RouteDTO[]): Route[] {
    return dtos.map(d => this.toDomain(d))
  },

  toFormDTO(domain: RouteForm): RouteFormDTO {
    return {
      name: domain.name,
      zone_id: domain.zoneId,
    }
  },

  zoneToDomain(dto: ZoneDTO): Zone {
    return {
      id: dto.id,
      name: dto.name,
      polygon: dto.polygon,
    }
  },

  zoneToDomainInList(dtos: ZoneDTO[]): Zone[] {
    return dtos.map(d => this.zoneToDomain(d))
  },

  mapDataToDomain(dto: MapDataDTO): MapData {
    return {
      zones: (dto.zones || []).map(z => ({
        id: z.id,
        name: z.name,
        polygon: z.polygon,
      })),
      customers: (dto.customers || []).map(c => ({
        id: c.id,
        latitude: c.latitude,
        longitude: c.longitude,
        zoneId: c.zone_id,
        routeId: c.route_id,
      })),
    }
  },
}
