import { NavItem } from '@/types'

const nav: Array<NavItem> = [
  {
    position: 100,
    label: 'access.title',
    icon: 'lock',
    children: [
      {
        to: { name: 'access.users' },
        label: 'access.users.title',
        permissions: ['manage access', 'manage users'],
      },
      {
        to: { name: 'access.roles' },
        label: 'access.roles.title',
        permissions: 'manage access',
      },
      {
        to: { name: 'access.permissions' },
        label: 'access.permissions.title',
        permissions: 'manage access',
      },
    ],
  },
]

export default nav
