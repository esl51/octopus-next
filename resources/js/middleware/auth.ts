import { useAuthStore } from '@/stores/auth'
import { MiddlewareInterface } from '@/types'

const authStore = useAuthStore()

export default ({ to }: MiddlewareInterface) => {
  if (!authStore.check) {
    return { name: 'auth.login', query: { intended_url: to?.fullPath } }
  }
}
