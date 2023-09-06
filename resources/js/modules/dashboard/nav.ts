import { NavItem } from '@/types'
import { IconDashboard } from '@tabler/icons-vue'

const nav: Array<NavItem> = [
  {
    position: -100,
    to: { name: 'dashboard' },
    label: 'dashboard.title',
    icon: IconDashboard,
  },
]

export default nav
