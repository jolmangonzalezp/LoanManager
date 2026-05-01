import httpClient from '@/Infrastructure/http/Client'
import type { LoginForm, AuthResponse } from '../types/Auth.Type'
import {UserDTO} from "../types/AuthDTO.Type";

const BASE = '/auth'

export const AuthApi = {
  async login(data: LoginForm): Promise<AuthResponse> {
    return httpClient.post<AuthResponse>(`${BASE}/login`, data)
  },

  async logout(): Promise<void> {
    return httpClient.post(`${BASE}/logout`)
  },

  async me(): Promise<UserDTO> {
    return httpClient.get<UserDTO>(`${BASE}/me`)
  }
}