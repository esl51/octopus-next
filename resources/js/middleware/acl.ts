import { useAuthStore } from '@/stores/auth'
import { MiddlewareInterface } from '@/types'
import { RouteLocationNormalized } from 'vue-router'

const authStore = useAuthStore()

export interface AclInterface extends MiddlewareInterface {
  to: RouteLocationNormalized
}

export default ({ to }: AclInterface) => {
  const permissions = ([] as Array<string | undefined>).concat(
    ...to.matched.map((r) => r.meta?.permissions),
  )
  let userAuthorized = false
  permissions.forEach((permission) => {
    if (permission !== undefined && authStore.can(permission)) {
      userAuthorized = true
    }
  })
  if (!userAuthorized) {
    return {
      name: 'not-found',
      params: { pathMatch: to.path.substring(1).split('/') },
      query: to.query,
      hash: to.hash,
    }
  }
}
