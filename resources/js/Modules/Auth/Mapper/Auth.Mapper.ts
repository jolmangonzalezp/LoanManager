import type { User, UserDTO } from '@/Modules/Auth'

export const AuthMapper = {
  toDomain(dto: UserDTO): User {
    return {
      id: dto.id,
      username: dto.username,
      name: dto.name || '',
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
