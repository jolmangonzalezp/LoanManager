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
  roles: string[]
  permissions: string[]
}
