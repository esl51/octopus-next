import { ListParams } from '@/types'
import { defineStore } from 'pinia'

interface State {
  lists: Record<string, ListParams>
}

export const useListsStore = defineStore('lists', {
  state: (): State => ({
    lists: {},
  }),

  actions: {
    update(key: string, params: ListParams) {
      const currentParams = {} as ListParams
      if (this.lists[key]) {
        Object.keys(params).forEach((paramKey) => {
          currentParams[paramKey] = this.lists[key][paramKey]
        })
      }
      this.lists[key] = {
        ...currentParams,
        ...params,
      }
    },
  },

  persist: true,
})
