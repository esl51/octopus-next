import { NavItem } from '@/types'

const nav: Array<NavItem> = [
  {
    position: -1,
    to: { name: 'files' },
    label: 'files.title',
    icon: 'files',
    permissions: ['manage files'],
  },
]

export default nav
