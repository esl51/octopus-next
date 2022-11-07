import { LangLocale, LangLocales } from '@/types'
import { defineStore } from 'pinia'

// defaults
const { locale, locales, fallbackLocale } = window.config

interface State {
  locale: LangLocale
  locales: LangLocales
  fallbackLocale: LangLocale
}

export const useLangStore = defineStore('lang', {
  state: (): State => ({
    locale,
    locales,
    fallbackLocale,
  }),

  actions: {
    setLocale(locale: LangLocale) {
      this.locale = locale
    },
  },

  persist: true,
})
