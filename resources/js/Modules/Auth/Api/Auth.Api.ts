import { Http } from '@/Infrastructure';
import { AuthResponse, LoginForm, UserDTO } from '@/Modules/Auth';

const BASE = '/auth'

export const AuthApi = {
  async login(data: LoginForm): Promise<AuthResponse> {
    return  await Http.post<AuthResponse>(`${BASE}/login`, data);
  },

  async logout(): Promise<void> {
    return Http.post(`${BASE}/logout`)
  },

  async me(): Promise<UserDTO> {
    return Http.get<UserDTO>(`${BASE}/me`)
  }
}
