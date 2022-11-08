import { setLanguage } from '@/plugins/i18n'
import { useAuthStore } from '@/stores/auth'
import { useLangStore } from '@/stores/lang'
import { MiddlewareInterface } from '@/types'

const langStore = useLangStore()
const authStore = useAuthStore()

export default ({ next }: MiddlewareInterface) => {
  setLanguage(langStore.locale)
  if (authStore.check) {
    authStore.fetchUser()
  }

  next()
}
