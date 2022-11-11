import accessRoutes from './access'
import authRoutes from './auth'
import { RouteRecordRaw } from 'vue-router'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'home',
    redirect: { name: 'dashboard' },
  },

  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/pages/PageDashboard.vue'),
    meta: {
      middleware: 'auth',
    },
  },

  ...authRoutes,
  ...accessRoutes,

  /*
  { path: '/media-manager', name: 'media-manager', component: () => import('@/pages/media-manager.vue'), meta: { permissions: ['manage media'] } },

  { path: '/pages', name: 'pages', component: () => import('@/pages/pages.vue'), meta: { permissions: ['manage pages'] } },

  {
    path: '/directories',
    name: 'directories',
    component: () => import('@/pages/directories/index.vue'),
    meta: { permissions: ['manage directories'] },
    children: [
      { path: 'statuses', name: 'directories.statuses', component: () => import('@/pages/directories/statuses.vue'), meta: { permissions: ['manage statuses'] } }
    ]
  },
  */

  {
    name: 'not-found',
    path: '/:catchAll(.*)',
    component: () => import('@/pages/errors/ErrorNotFound.vue'),
    meta: {
      layout: 'error',
    },
  },
]

export default routes
