import { Http } from '@/Infrastructure';
import type { RouteDTO, RouteFormDTO, ZoneDTO, MapDataDTO } from '@/Modules/Route';

const BASE = '/routes';
const ZONES_BASE = '/zones';

export const RouteApi = {

  async getAll(): Promise<RouteDTO[]> {
    return  await Http.get<RouteDTO[]>(BASE);
  },

  async getById(id: string): Promise<RouteDTO> {
    return Http.get<RouteDTO>(`${BASE}/${id}`);
  },

  async create(data: RouteFormDTO): Promise<boolean> {
    return Http.post<boolean>(BASE, data);
  },

  async update(id: string, data: RouteFormDTO): Promise<boolean> {
    return Http.put<boolean>(`${BASE}/${id}`, data);
  },

  async delete(id: string): Promise<boolean> {
    return Http.delete<boolean>(`${BASE}/${id}`);
  },

  async assignUsers(id: string, userIds: string[]): Promise<boolean> {
    return Http.post<boolean>(`${BASE}/${id}/users`, { user_ids: userIds });
  },

  async removeUser(id: string, userId: string): Promise<boolean> {
    return Http.delete<boolean>(`${BASE}/${id}/users/${userId}`);
  },

  async getMapData(): Promise<MapDataDTO> {
    return Http.get<MapDataDTO>(`${BASE}/map-data`);
  },

  async getZones(): Promise<ZoneDTO[]> {
    return Http.get<ZoneDTO[]>(ZONES_BASE);
  },

  async getZoneById(id: string): Promise<ZoneDTO> {
    return Http.get<ZoneDTO>(`${ZONES_BASE}/${id}`);
  },

  async createZone(data: { name: string; polygon: number[][] }): Promise<boolean> {
    return Http.post<boolean>(ZONES_BASE, data);
  },

  async updateZone(id: string, data: { name: string; polygon: number[][] }): Promise<boolean> {
    return Http.put<boolean>(`${ZONES_BASE}/${id}`, data);
  },

  async deleteZone(id: string): Promise<boolean> {
    return Http.delete<boolean>(`${ZONES_BASE}/${id}`);
  },
}
