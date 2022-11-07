import { usePageStore } from '@/stores/page'
import { BreadcrumbItem } from '@/types'

interface PageConfig {
  title: string
  breadcrumb?: Array<BreadcrumbItem>
}

export function usePage(config: PageConfig) {
  const { title, breadcrumb } = config

  // store
  const pageStore = usePageStore()
  pageStore.setTitle(title)
  pageStore.setBreadcrumb(breadcrumb ?? [])
}
