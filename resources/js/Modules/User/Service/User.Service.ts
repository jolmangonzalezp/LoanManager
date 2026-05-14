import { UserApi, UserMapper } from '@/Modules/User';
import type { User, UserForm, Role, Permission } from '@/Modules/User';

export const UserService = {
  async getAll(): Promise<User[]> {
    const response = await UserApi.getAll();
    return UserMapper.toDomainInList(response);
  },

  async getById(id: string): Promise<User> {
    const response = await UserApi.getById(id);
    return UserMapper.toDomain(response);
  },

  async create(data: UserForm): Promise<void> {
    const payload: Record<string, any> = {
      username: data.username,
      name: {
        first_name: data.name.firstName,
        middle_name: data.name.middleName,
        last_name: data.name.lastName,
        second_last_name: data.name.secondLastName,
      },
      email: data.email,
      phone: data.phone,
    };
    if (data.password) payload.password = data.password;
    await UserApi.create(payload);
  },

  async update(id: string, data: Partial<UserForm>): Promise<User> {
    const payload: Record<string, any> = {
      username: data.username,
      email: data.email,
      phone: data.phone,
    };
    if (data.name) {
      payload.name = {
        first_name: data.name.firstName,
        middle_name: data.name.middleName,
        last_name: data.name.lastName,
        second_last_name: data.name.secondLastName,
      };
    }
    const response = await UserApi.update(id, payload);
    return UserMapper.toDomain(response);
  },

  async delete(id: string): Promise<void> {
    return UserApi.delete(id);
  },

  async assignRoles(id: string, roles: string[]): Promise<void> {
    return UserApi.assignRoles(id, roles);
  },

  async getPermissions(id: string): Promise<string[]> {
    const response = await UserApi.getPermissions(id);
    return response.permissions;
  },

  async getRoles(id: string): Promise<string[]> {
    const response = await UserApi.getRoles(id);
    return response.roles;
  },

  async getRolesAll(): Promise<Role[]> {
    const response = await UserApi.getRolesAll();
    return UserMapper.roleToDomainInList(response);
  },

  async createRole(data: Record<string, any>): Promise<Role> {
    const response = await UserApi.createRole(data);
    return UserMapper.roleToDomain(response);
  },

  async updateRole(id: string, data: Record<string, any>): Promise<Role> {
    const response = await UserApi.updateRole(id, data);
    return UserMapper.roleToDomain(response);
  },

  async deleteRole(id: string): Promise<void> {
    return UserApi.deleteRole(id);
  },

  async getPermissionsAll(): Promise<Permission[]> {
    const response = await UserApi.getPermissionsAll();
    return UserMapper.permissionToDomainInList(response);
  },

  async createPermission(data: Record<string, any>): Promise<Permission> {
    const response = await UserApi.createPermission(data);
    return UserMapper.permissionToDomain(response);
  },
};
