import { RouteRecordRaw } from 'vue-router'

const routes: Array<RouteRecordRaw> = [
  // files
  {
    path: '/files',
    name: 'files',
    component: () => import('./pages/PageFiles.vue'),
    meta: {
      middleware: ['auth', 'acl'],
      permissions: ['manage files'],
    },
  },
]

export default routes
