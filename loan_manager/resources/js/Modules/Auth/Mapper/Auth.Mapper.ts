import { User, UserDTO } from '@/Modules/Auth';

export const AuthMapper = {
    toDomain(dto: UserDTO): User {
        return {
            id: dto.id,
            email: dto.email,
            name: dto.name,
            lastname: dto.lastname,
            createdAt: dto.created_at,
        }
    },

    toDomainInList(dtos: UserDTO[]): User[] | void[] {
        return dtos.map(dto => {this.toDomain(dto)})
    }
}
