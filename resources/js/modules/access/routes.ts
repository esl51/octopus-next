import { RouteRecordRaw } from 'vue-router'

const routes: Array<RouteRecordRaw> = [
  // users
  {
    path: '/access/users',
    name: 'access.users',
    component: () => import('./pages/PageUsers.vue'),
    meta: {
      middleware: ['auth', 'acl'],
      permissions: ['manage access', 'manage users'],
    },
  },

  // roles
  {
    path: '/access/roles',
    name: 'access.roles',
    component: () => import('./pages/PageRoles.vue'),
    meta: {
      middleware: ['auth', 'acl'],
      permissions: ['manage access'],
    },
  },

  // permissions
  {
    path: '/access/permissions',
    name: 'access.permissions',
    component: () => import('./pages/PagePermissions.vue'),
    meta: {
      middleware: ['auth', 'acl'],
      permissions: ['manage access'],
    },
  },
]

export default routes
