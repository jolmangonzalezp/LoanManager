import { useRouter } from 'vue-router'
import httpClient from '@/Infrastructure/http/Client'
import { AuthApi } from '../Api/Auth.Api'
import type { LoginForm, AuthResponse } from '../types/Auth.Type'

export const AuthService = {
  async login(data: LoginForm): Promise<AuthResponse> {
    const response = await AuthApi.login(data)
    httpClient.setToken(response.token)
    return response
  },

  logout() {
    httpClient.setToken(null)
    window.location.href = '/login'
  },

  isAuthenticated(): boolean {
    return httpClient.getToken() !== null
  }
}