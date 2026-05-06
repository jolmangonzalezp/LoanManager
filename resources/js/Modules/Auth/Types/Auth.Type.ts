export interface LoginForm {
  email: string
  password: string
}

export interface AuthResponse {
  token: string
  user?: any
}

export interface User {
  id: string
  email: string
  name: string
  lastname: string
  createdAt: string
}