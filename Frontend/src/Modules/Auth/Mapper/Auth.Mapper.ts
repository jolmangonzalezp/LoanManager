import {UserDTO} from "../types/AuthDTO.Type";
import {User} from "../types/Auth.Type";

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

    toDomainInList(dtos: UserDTO[]): User[] {
        return dtos.map(dto => {this.toDomain(dto)})
    }
}