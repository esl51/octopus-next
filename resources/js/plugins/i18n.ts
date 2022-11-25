import messages from '@/lang'
import axios from 'axios'
import { createI18n } from 'vue-i18n'

const DEFAULT_LANGUAGE = window.config.locale

const i18n = createI18n({
  legacy: false,
  locale: DEFAULT_LANGUAGE,
  fallbackLocale: window.config.fallbackLocale,
  globalInjection: true,
  messages,
})

export const setLanguage = (lang: keyof typeof messages) => {
  i18n.global.locale.value = lang
  axios.defaults.headers.common['Accept-Language'] = lang
  document.querySelector('html')?.setAttribute('lang', lang)
  return lang
}

export default i18n
