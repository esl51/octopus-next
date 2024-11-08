import { useAuthStore } from '@/stores/auth'
import { MiddlewareInterface } from '@/types'

const authStore = useAuthStore()

export default ({ to, next }: MiddlewareInterface) => {
  if (!authStore.check) {
    return next({ name: 'auth.login', query: { intended_url: to?.fullPath } })
  }
  return next()
}
