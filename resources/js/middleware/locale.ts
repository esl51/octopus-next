import i18n, { setLanguage } from '@/plugins/i18n'
import { useAuthStore } from '@/stores/auth'
import { useLangStore } from '@/stores/lang'
import { MiddlewareInterface } from '@/types'

const langStore = useLangStore()
const authStore = useAuthStore()

export default ({ next }: MiddlewareInterface) => {
  const needToFetchUser =
    authStore.check && langStore.locale !== i18n.global.locale.value
  setLanguage(langStore.locale)
  if (needToFetchUser) {
    authStore.fetchUser()
  }

  next()
}
