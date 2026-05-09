import type { Permission, Role, RoleForm, User, UserForm, UserPermissions } from '@/Modules/User';
import type { CreatedUserDTO, PermissionDTO, RoleDTO, UserDTO, UserPermissionsDTO } from '@/Modules/User';

export const UserMapper = {
  toDomain(dto: UserDTO): User {
    return {
      id: dto.id,
      username: dto.username,
      name: dto.name || '',
      email: dto.email || '',
      phone: dto.phone || '',
      createdAt: dto.created_at,
      enabled: dto.enabled,
    };
  },

  toDomainInList(dtos: UserDTO[]): User[] {
    return dtos.map(d => this.toDomain(d));
  },

  toForm(user: User): UserForm {
    return {
      username: user.username,
      name: user.name,
      email: user.email,
      phone: user.phone,
    };
  },

  roleToDomain(dto: RoleDTO): Role {
    return {
      id: dto.id,
      slug: dto.slug,
      name: dto.name,
      description: dto.description || '',
      isSystem: dto.is_system,
      createdAt: dto.created_at,
    };
  },

  roleToDomainInList(dtos: RoleDTO[]): Role[] {
    return dtos.map(d => this.roleToDomain(d));
  },

  permissionToDomain(dto: PermissionDTO): Permission {
    return {
      id: dto.id,
      slug: dto.slug,
      name: dto.name,
      description: dto.description || '',
      group: dto.group || '',
      createdAt: dto.created_at,
    };
  },

  permissionToDomainInList(dtos: PermissionDTO[]): Permission[] {
    return dtos.map(d => this.permissionToDomain(d));
  },
};
