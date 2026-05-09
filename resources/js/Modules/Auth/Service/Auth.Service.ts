import { Http } from '@/Infrastructure';
import { AuthApi, AuthMapper, AuthResponse, LoginForm, User, UserDTO } from '@/Modules/Auth';

export const AuthService = {
  async login(data: LoginForm): Promise<AuthResponse> {
    const response = await AuthApi.login(data);
    Http.setToken(response.token);
    return response;
  },

  async me(): Promise<User> {
    const response: UserDTO =  await AuthApi.me();
    return AuthMapper.toDomain(response);
  },

  async logout(): Promise<void> {
      Http.setToken(null);
      await AuthApi.logout();
  },

  isAuthenticated(): boolean {
    return Http.getToken() !== null
  }
}
