import { ItemsApi } from '@/types'
import { Permission, Role, User, UsersApi } from './types'
import itemsApi from '@/api/items'
import api from '@/api'

export const accessUrls = {
  users: '/api/access/users',
  roles: '/api/access/roles',
  permissions: '/api/access/permissions',
}

const items: ItemsApi<User> = itemsApi(accessUrls.users)

export const usersApi: UsersApi<User> = {
  ...items,

  async disable(id: number): Promise<User> {
    const { data } = await api.post(items.url + '/' + id + '/disable')
    return data.data as User
  },

  async enable(id: number): Promise<User> {
    const { data } = await api.post(items.url + '/' + id + '/enable')
    return data.data as User
  },
}
export const rolesApi: ItemsApi<Role> = itemsApi(accessUrls.roles)
export const permissionsApi: ItemsApi<Permission> = itemsApi(
  accessUrls.permissions,
)
