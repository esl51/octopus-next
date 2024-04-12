import { NavItem } from '@/types'
import { IconBooks } from '@tabler/icons-vue'

const nav: Array<NavItem> = [
  {
    position: 90,
    label: 'directory.title',
    icon: IconBooks,
    children: [
      {
        to: { name: 'directory.propertyGroups' },
        label: 'properties.property_groups.title',
        permissions: ['manage properties'],
      },
      {
        to: { name: 'directory.properties' },
        label: 'properties.title',
        permissions: ['manage properties'],
      },
    ],
  },
]

export default nav
