import axios, { AxiosError, AxiosStatic } from 'axios'

export type Api = AxiosStatic

export type ApiError = AxiosError

const api: Api = axios

export default api
