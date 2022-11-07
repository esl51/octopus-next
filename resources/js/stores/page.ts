import { BreadcrumbItem } from '@/types'
import { defineStore } from 'pinia'

// defaults
const title = window.config.name
const breadcrumb = [] as Array<BreadcrumbItem>

interface State {
  title: string
  breadcrumb: Array<BreadcrumbItem>
}

export const usePageStore = defineStore('page', {
  state: (): State => ({
    title,
    breadcrumb,
  }),

  actions: {
    setTitle(title: string) {
      this.title = title
    },
    setBreadcrumb(breadcrumb: Array<BreadcrumbItem>) {
      this.breadcrumb = breadcrumb
    },
  },
})
