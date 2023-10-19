import itemsApi from '@/api/items'
import { ItemsApi } from '@/types'
import { Permission, Role, User } from './types'

export const accessUrls = {
  users: '/api/access/users',
  roles: '/api/access/roles',
  permissions: '/api/access/permissions',
}

export const usersApi: ItemsApi<User> = itemsApi(accessUrls.users)
export const rolesApi: ItemsApi<Role> = itemsApi(accessUrls.roles)
export const permissionsApi: ItemsApi<Permission> = itemsApi(
  accessUrls.permissions,
)
