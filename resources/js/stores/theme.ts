import { defineStore } from 'pinia'

interface State {
  theme: string
}

export const useThemeStore = defineStore('theme', {
  state: (): State => ({
    theme: 'light',
  }),

  actions: {
    toggleTheme() {
      this.theme = this.theme === 'light' ? 'dark' : 'light'
    },
  },

  persist: true,
})
