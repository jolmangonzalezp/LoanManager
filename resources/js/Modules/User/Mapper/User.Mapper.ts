import type { Permission, Role, RoleForm, User, UserForm, UserPermissions } from '@/Modules/User';
import type { CreatedUserDTO, PermissionDTO, RoleDTO, UserDTO, UserPermissionsDTO } from '@/Modules/User';

export const UserMapper = {
  toDomain(dto: UserDTO): User {
    return {
      id: dto.id,
      username: dto.username,
      name: dto.name
        ? {
            firstName: dto.name.first_name || '',
            middleName: dto.name.middle_name || null,
            lastName: dto.name.last_name || '',
            secondLastName: dto.name.second_last_name || '',
          }
        : null,
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
      name: user.name
        ? {
            firstName: user.name.firstName,
            middleName: user.name.middleName || '',
            lastName: user.name.lastName,
            secondLastName: user.name.secondLastName,
          }
        : { firstName: '', middleName: '', lastName: '', secondLastName: '' },
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
