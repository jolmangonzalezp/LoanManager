export interface UserDTO {
  id: string
  username: string
  name: string | null
  email: string | null
  phone: string | null
  created_at: string
  enabled: boolean
  roles: string[]
  permissions: string[]
}
