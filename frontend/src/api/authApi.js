import axiosClient from "./axiosClient";
import { SERVICE_PATHS } from "./apiConfig";

const AUTH_PREFIX = SERVICE_PATHS.AUTH;

export const authApi = {
  login: (credentials) => axiosClient.post(`${AUTH_PREFIX}/login`, credentials),
  register: (data) => axiosClient.post(`${AUTH_PREFIX}/register`, data),
  getProfile: () => axiosClient.get(`${AUTH_PREFIX}/me`),
  logout: () => axiosClient.post(`${AUTH_PREFIX}/logout`),
};