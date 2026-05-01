import { useRouter } from 'vue-router'
import httpClient from '@/Infrastructure/http/Client'
import { AuthApi } from '../Api/Auth.Api'
import type {LoginForm, AuthResponse, User} from '../types/Auth.Type'
import {UserDTO} from "../types/AuthDTO.Type";
import {AuthMapper} from "../Mapper/Auth.Mapper";

export const AuthService = {
  async login(data: LoginForm): Promise<AuthResponse> {
    const response = await AuthApi.login(data)
    httpClient.setToken(response.token)
    return response
  },

  async me(): Promise<User> {
    const response: UserDTO =  await AuthApi.me();
    return AuthMapper.toDomain(response);
  },

  logout() {
    httpClient.setToken(null)
    window.location.href = '/login'
  },

  isAuthenticated(): boolean {
    return httpClient.getToken() !== null
  }
}