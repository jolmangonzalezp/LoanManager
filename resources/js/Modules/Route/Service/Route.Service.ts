import { RouteApi, RouteMapper } from '@/Modules/Route';
import type { Route, RouteForm, Zone, MapData } from '@/Modules/Route';

export const RouteService = {

  async getAll(): Promise<Route[]> {
    const response = await RouteApi.getAll();
    return RouteMapper.toDomainInList(response);
  },

  async getById(id: string): Promise<Route> {
    const response = await RouteApi.getById(id);
    return RouteMapper.toDomain(response);
  },

  async create(data: RouteForm): Promise<boolean> {
    const mapped = RouteMapper.toFormDTO(data);
    return RouteApi.create(mapped);
  },

  async update(id: string, data: RouteForm): Promise<boolean> {
    const mapped = RouteMapper.toFormDTO(data);
    return RouteApi.update(id, mapped);
  },

  async delete(id: string): Promise<boolean> {
    return RouteApi.delete(id);
  },

  async assignUsers(id: string, userIds: string[]): Promise<boolean> {
    return RouteApi.assignUsers(id, userIds);
  },

  async removeUser(id: string, userId: string): Promise<boolean> {
    return RouteApi.removeUser(id, userId);
  },

  async getMapData(): Promise<MapData> {
    const response = await RouteApi.getMapData();
    return RouteMapper.mapDataToDomain(response);
  },

  async getZones(): Promise<Zone[]> {
    const response = await RouteApi.getZones();
    return RouteMapper.zoneToDomainInList(response);
  },

  async createZone(name: string, polygon: number[][]): Promise<boolean> {
    return RouteApi.createZone({ name, polygon });
  },

  async updateZone(id: string, name: string, polygon: number[][]): Promise<boolean> {
    return RouteApi.updateZone(id, { name, polygon });
  },

  async deleteZone(id: string): Promise<boolean> {
    return RouteApi.deleteZone(id);
  },
}
