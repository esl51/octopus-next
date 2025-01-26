import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

export default async () => {
  if (authStore.check) {
    return { name: 'home' }
  }
}
