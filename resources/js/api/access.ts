import itemsApi, { ItemsApi } from './items'

export type UsersApi = ItemsApi
export type RolesApi = ItemsApi
export type PermissionsApi = ItemsApi

export const accessUrls = {
  users: '/api/access/users',
  roles: '/api/access/roles',
  permissions: '/api/access/permissions',
}

export const usersApi: UsersApi = itemsApi(accessUrls.users)
export const rolesApi: RolesApi = itemsApi(accessUrls.roles)
export const permissionsApi: PermissionsApi = itemsApi(accessUrls.permissions)
