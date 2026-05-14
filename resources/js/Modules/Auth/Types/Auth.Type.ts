export interface LoginForm {
  login: string
  password: string
}

export interface AuthResponse {
  token: string
  user?: any
}

export interface UserName {
  firstName: string
  middleName: string | null
  lastName: string
  secondLastName: string
}

export interface User {
  id: string
  username: string
  name: UserName | null
  email: string
  phone: string
  createdAt: string
  enabled: boolean
  roles: string[]
  permissions: string[]
}
