import messages from '@/lang'
import { FunctionalComponent, SVGAttributes } from 'vue'
import {
  NavigationGuardNext,
  RouteLocationNormalized,
  RouteLocationRaw,
} from 'vue-router'

export interface SVGProps extends Partial<SVGAttributes> {
  size?: 24 | number | string
  strokeWidth?: number | string
}

export type BreadcrumbItem = {
  to: RouteLocationRaw
  text: string
}

export type NavItem = {
  position?: number
  label: string
  to?: RouteLocationRaw
  icon?: FunctionalComponent<SVGProps>
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

export interface MiddlewareContext {
  to: RouteLocationNormalized
  from: RouteLocationNormalized
  next: NavigationGuardNext
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

export interface ListResponse<T extends Item> {
  data: Array<T>
  meta: Meta
}

export interface ItemsApi<T extends Item> {
  url: string
  all: () => Promise<ListResponse<T>>
  list: (params?: ListParams) => Promise<ListResponse<T>>
  get: (id: number) => Promise<T>
  store: (payload: Record<string, unknown>) => Promise<T>
  update: (id: number, payload: Record<string, unknown>) => Promise<T>
  destroy: (id: number) => Promise<void>
  moveAfter: (id: number, afterId: number) => Promise<T>
  moveBefore: (id: number, beforeId: number) => Promise<T>
}

export interface OTableColumn {
  key: string
  title?: string
  sortable?: boolean
  class?: string
  disabled?: boolean
  formatter?:
    | 'date'
    | 'datetime'
    | 'time'
    | 'money'
    | 'filesize'
    | ((value: unknown) => string)
}

export interface ItemAction {
  label: string
  icon?: FunctionalComponent<SVGProps>
  variant?: string
  disabled?: boolean
  hidden?: boolean
  handler: () => void
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
