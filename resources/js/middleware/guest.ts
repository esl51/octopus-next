import { useAuthStore } from '@/stores/auth'
import { MiddlewareInterface } from '@/types'

const authStore = useAuthStore()

export default ({ next }: MiddlewareInterface) => {
  if (authStore.check) {
    next({ name: 'home' })
  } else {
    next()
  }
}
