import { RouteRecordRaw } from 'vue-router'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('./pages/PageDashboard.vue'),
    meta: {
      middleware: 'auth',
    },
  },
]

export default routes
