export interface UserNameDTO {
  first_name: string | null
  middle_name: string | null
  last_name: string | null
  second_last_name: string | null
}

export interface UserDTO {
  id: string
  username: string
  name: UserNameDTO | null
  email: string | null
  phone: string | null
  created_at: string
  enabled: boolean
}

export interface CreatedUserDTO {
  id: string
  username: string
  name: UserNameDTO | null
  email: string | null
  created_at: string
}

export interface RoleDTO {
  id: string
  slug: string
  name: string
  description: string | null
  is_system: boolean
  created_at: string
}

export interface PermissionDTO {
  id: string
  slug: string
  name: string
  description: string | null
  group: string | null
  created_at: string
}

export interface UserPermissionsDTO {
  permissions: string[]
}

export interface UserRolesDTO {
  roles: string[]
}
