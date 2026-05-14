import type { User, UserDTO } from '@/Modules/Auth'

export const AuthMapper = {
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
      roles: dto.roles ?? [],
      permissions: dto.permissions ?? [],
    }
  },

  toDomainInList(dtos: UserDTO[]): User[] {
    return dtos.map(d => this.toDomain(d))
  },
}
