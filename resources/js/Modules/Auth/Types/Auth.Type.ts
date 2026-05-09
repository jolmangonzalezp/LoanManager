export interface LoginForm {
  login: string
  password: string
}

export interface AuthResponse {
  token: string
  user?: any
}

export interface User {
  id: string
  username: string
  name: string
  email: string
  phone: string
  createdAt: string
  enabled: boolean
  roles: string[]
  permissions: string[]
}
