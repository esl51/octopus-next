import { RouteRecordRaw } from 'vue-router'

const modules: Array<RouteRecordRaw> = []
Object.entries(
  import.meta.glob('../modules/*/routes.ts', { eager: true, import: 'default' })
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
).forEach(([path, definition]) => {
  if (definition) {
    modules.push(...(definition as Array<RouteRecordRaw>))
  }
})

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'home',
    redirect: { name: 'dashboard' },
  },

  ...modules,

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
    path: '/:pathMatch(.*)*',
    component: () => import('@/pages/errors/ErrorNotFound.vue'),
    meta: {
      layout: 'error',
    },
  },
]

export default routes
