import itemsApi, { ItemsApi } from '@/api/items'

export const accessUrls = {
  users: '/api/access/users',
  roles: '/api/access/roles',
  permissions: '/api/access/permissions',
}

export const usersApi: ItemsApi = itemsApi(accessUrls.users)
export const rolesApi: ItemsApi = itemsApi(accessUrls.roles)
export const permissionsApi: ItemsApi = itemsApi(accessUrls.permissions)
