import { ApiError } from '@/api'
import authApi from '@/api/auth'
import { User } from '@/modules/access/types'
import { defineStore } from 'pinia'

interface State {
  user: User | null
}

export const useAuthStore = defineStore('auth', {
  state: (): State => ({
    user: null,
  }),

  getters: {
    check: (state: State): boolean => state.user !== null,
  },

  actions: {
    updateUser(user: User) {
      this.user = user
    },
    async fetchUser() {
      const me = await authApi.user()
      this.updateUser(me)
    },
    async logout() {
      this.$reset()
      try {
        await authApi.logout()
      } catch (e) {
        const err = e as ApiError
        if (err.response?.status !== 401) {
          throw err
        }
      }
    },
    can(permission: string): boolean {
      return this.user?.can !== undefined && this.user.can[permission] === true
    },
    canAny(permissions: Array<string> | string): boolean {
      if (typeof permissions === 'string') {
        return this.can(permissions)
      }
      return permissions.filter((permission) => this.can(permission)).length > 0
    },
  },

  persist: true,
})
