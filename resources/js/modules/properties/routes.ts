import { h } from 'vue'
import { RouteRecordRaw, RouterView } from 'vue-router'

const routes: Array<RouteRecordRaw> = [
  // property groups
  {
    path: '/directory/property-groups',
    name: 'directory.propertyGroups',
    component: () => import('./pages/PagePropertyGroups.vue'),
    meta: {
      middleware: ['auth', 'acl'],
      permissions: ['manage properties'],
    },
  },

  // properties
  {
    path: '/directory/properties',
    name: 'directory.properties',
    component: { render: () => h(RouterView) },
    redirect: { name: 'directory.properties.list' },
    children: [
      // properties
      {
        path: '',
        name: 'directory.properties.list',
        component: () => import('./pages/PageProperties.vue'),
        meta: {
          middleware: ['auth', 'acl'],
          permissions: ['manage properties'],
        },
      },

      // property
      {
        path: ':id',
        name: 'directory.property',
        component: () => import('./pages/PageProperty.vue'),
        meta: {
          middleware: ['auth', 'acl'],
          permissions: ['manage properties'],
        },
      },
    ],
  },
]

export default routes
