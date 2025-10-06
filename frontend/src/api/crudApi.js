import axiosClient from "./axiosClient";
import { SERVICE_PATHS } from "./apiConfig";

const CRUD_PREFIX = SERVICE_PATHS.CRUD;

export const crudApi = {
  getAllProperties: () => axiosClient.get(`${CRUD_PREFIX}/properties`),
  getPropertyById: (id) => axiosClient.get(`${CRUD_PREFIX}/properties/${id}`),
  createProperty: (data) => axiosClient.post(`${CRUD_PREFIX}/properties`, data),
  updateProperty: (id, data) =>
    axiosClient.put(`${CRUD_PREFIX}/properties/${id}`, data),
  deleteProperty: (id) => axiosClient.delete(`${CRUD_PREFIX}/properties/${id}`),
};
