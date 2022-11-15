import messages from '@/lang'
import {
  NavigationGuardNext,
  RouteLocationNormalized,
  RouteLocationRaw,
} from 'vue-router'

export type BreadcrumbItem = {
  to: RouteLocationRaw
  text: string
}

export type NavItem = {
  position?: number
  label: string
  to?: RouteLocationRaw
  icon?: string
  children?: Array<NavItem>
  permissions?: Array<string> | string
}

export type Toast = {
  id?: number
  variant?: string
  timeout?: number
  title?: string
  body?: string
  hint?: string
}

export type LangLocale = keyof typeof messages

export type LangLocales = {
  [key in LangLocale]: string
}

export interface MiddlewareInterface {
  to?: RouteLocationNormalized
  from?: RouteLocationNormalized
  next: NavigationGuardNext
}

export interface ListParams {
  id?: number
  search?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_desc?: number
  [key: string]: unknown
}

export interface Translation {
  id: number
  locale: keyof typeof messages
  created_at: Date
  updated_at: Date
  [key: string]: unknown
}

export interface Item {
  id: number
  is_deletable: boolean
  is_editable: boolean
  created_at: Date
  updated_at: Date
  translations?: Array<Translation>
  [key: string]: unknown
}

export interface Meta {
  current_page: number
  from: number
  last_page: number
  per_page: number
  to: number
  total: number
}

export interface ListResponse {
  data: Array<Item>
  meta: Meta
}

export interface ItemsApi {
  url: string
  all: () => Promise<ListResponse>
  list: (params?: ListParams) => Promise<ListResponse>
  get: (id: number) => Promise<Item>
  store: (payload: Record<string, unknown>) => Promise<Item>
  update: (id: number, payload: Record<string, unknown>) => Promise<Item>
  destroy: (id: number) => Promise<void>
}

export interface Role extends Item {
  id: number
  name: string
  title: string
}

export interface User extends Item {
  id: number
  name: string
  email: string
  email_verified_at: Date | null
  can?: { [permission: string]: boolean }
  roles?: Array<Role>
  name_placeholder: string
  photo_url: string | null
}

export interface OTableColumn {
  key: string
  title?: string
  sortable?: boolean
  class?: string
}

export type AppConfig = {
  name: string
  locale: LangLocale
  locales: LangLocales
  fallbackLocale: LangLocale
  authFeatures: Array<string>
}

declare global {
  interface Window {
    config: AppConfig
  }
}
