import { Http } from '@/Infrastructure';
import type { CreatedUserDTO, PermissionDTO, RoleDTO, UserDTO, UserPermissionsDTO, UserRolesDTO } from '@/Modules/User';

const BASE = '/users';
const ROLES_BASE = '/roles';
const PERMISSIONS_BASE = '/permissions';

export const UserApi = {
  async getAll(): Promise<UserDTO[]> {
    return Http.get<UserDTO[]>(BASE);
  },

  async getById(id: string): Promise<UserDTO> {
    return Http.get<UserDTO>(`${BASE}/${id}`);
  },

  async create(data: Record<string, any>): Promise<CreatedUserDTO> {
    return Http.post<CreatedUserDTO>(BASE, data);
  },

  async update(id: string, data: Record<string, any>): Promise<UserDTO> {
    return Http.put<UserDTO>(`${BASE}/${id}`, data);
  },

  async delete(id: string): Promise<void> {
    return Http.delete(`${BASE}/${id}`);
  },

  async assignRoles(id: string, roles: string[]): Promise<void> {
    return Http.post(`${BASE}/${id}/roles`, { roles });
  },

  async getRoles(id: string): Promise<UserRolesDTO> {
    return Http.get<UserRolesDTO>(`${BASE}/${id}/roles`);
  },

  async getPermissions(id: string): Promise<UserPermissionsDTO> {
    return Http.get<UserPermissionsDTO>(`${BASE}/${id}/permissions`);
  },

  async getRolesAll(): Promise<RoleDTO[]> {
    return Http.get<RoleDTO[]>(ROLES_BASE);
  },

  async getRoleById(id: string): Promise<RoleDTO> {
    return Http.get<RoleDTO>(`${ROLES_BASE}/${id}`);
  },

  async createRole(data: Record<string, any>): Promise<RoleDTO> {
    return Http.post<RoleDTO>(ROLES_BASE, data);
  },

  async updateRole(id: string, data: Record<string, any>): Promise<RoleDTO> {
    return Http.put<RoleDTO>(`${ROLES_BASE}/${id}`, data);
  },

  async deleteRole(id: string): Promise<void> {
    return Http.delete(`${ROLES_BASE}/${id}`);
  },

  async getPermissionsAll(): Promise<PermissionDTO[]> {
    return Http.get<PermissionDTO[]>(PERMISSIONS_BASE);
  },

  async createPermission(data: Record<string, any>): Promise<PermissionDTO> {
    return Http.post<PermissionDTO>(PERMISSIONS_BASE, data);
  },
};
