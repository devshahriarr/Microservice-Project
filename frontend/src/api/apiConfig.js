// export const AUTH_API = import.meta.env.VITE_AUTH_SERVICE_URL;
// export const CRUD_API = import.meta.env.VITE_CRUD_SERVICE_URL;

export const API_GATEWAY_URL = import.meta.env.VITE_API_GATEWAY_URL;

export const SERVICE_PATHS = {
  AUTH: `${API_GATEWAY_URL}/auth`,
  CRUD: `${API_GATEWAY_URL}/crud`,
};