import { useAuthStore } from '@/stores/auth'
import { MiddlewareInterface } from '@/types'

const authStore = useAuthStore()

export default async ({ next }: MiddlewareInterface) => {
  if (authStore.check) {
    return next({ name: 'home' })
  }
  return next()
}
