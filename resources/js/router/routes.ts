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

  /*

  { path: '/media-manager', name: 'media-manager', component: () => import('@/pages/media-manager.vue'), meta: { permissions: ['manage media'] } },

  { path: '/pages', name: 'pages', component: () => import('@/pages/pages.vue'), meta: { permissions: ['manage pages'] } },

  {
    path: '/settings',
    component: () => import('@/pages/settings/index.vue'),
    children: [
      { path: '', name: 'settings', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: () => import('@/pages/settings/profile.vue') },
      { path: 'password', name: 'settings.password', component: () => import('@/pages/settings/password.vue') }
    ]
  },

  {
    path: '/directories',
    name: 'directories',
    component: () => import('@/pages/directories/index.vue'),
    meta: { permissions: ['manage directories'] },
    children: [
      { path: 'statuses', name: 'directories.statuses', component: () => import('@/pages/directories/statuses.vue'), meta: { permissions: ['manage statuses'] } }
    ]
  },

  {
    path: '/access',
    name: 'access',
    component: () => import('@/pages/access/index.vue'),
    meta: { permissions: ['manage users', 'manage access'] },
    children: [
      { path: 'users', name: 'access.users', component: () => import('@/pages/access/users.vue'), meta: { permissions: ['manage users'] } },
      { path: 'roles', name: 'access.roles', component: () => import('@/pages/access/roles.vue'), meta: { permissions: ['manage access'] } },
      { path: 'permissions', name: 'access.permissions', component: () => import('@/pages/access/permissions.vue'), meta: { permissions: ['manage access'] } }
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
