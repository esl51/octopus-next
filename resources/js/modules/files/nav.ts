import { NavItem } from '@/types'
import { IconFiles } from '@tabler/icons-vue'

const nav: Array<NavItem> = [
  {
    position: -1,
    to: { name: 'files' },
    label: 'files.title',
    icon: IconFiles,
    permissions: ['manage files'],
  },
]

export default nav
