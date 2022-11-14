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
  label: string
  to?: RouteLocationRaw
  icon?: string
  children?: Array<NavItem>
  permissions: Array<string> | string
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

export type Role = {
  id: number
  name: string
  title: string
}

export interface User {
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
