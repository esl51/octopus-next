import { RouteRecordRaw } from 'vue-router'

const { authFeatures } = window.config

const routes: Array<RouteRecordRaw> = [
  {
    path: '/login',
    name: 'auth.login',
    component: () => import('./pages/PageLogin.vue'),
    meta: {
      layout: 'auth',
      middleware: 'guest',
    },
  },
]

// registration
if (authFeatures.includes('registration')) {
  routes.push({
    path: '/register',
    name: 'auth.register',
    component: () => import('./pages/PageRegister.vue'),
    meta: {
      layout: 'auth',
      middleware: 'guest',
    },
  })
}

// reset passwords
if (authFeatures.includes('reset-passwords')) {
  routes.push({
    path: '/forgot-password',
    name: 'auth.forgotPassword',
    component: () => import('./pages/PageForgotPassword.vue'),
    meta: {
      layout: 'auth',
      middleware: 'guest',
    },
  })
  routes.push({
    path: '/reset-password',
    name: 'auth.resetPassword',
    component: () => import('./pages/PageResetPassword.vue'),
    meta: {
      layout: 'auth',
      middleware: 'guest',
    },
  })
}

// update profile information and password
if (
  authFeatures.includes('update-profile-information') ||
  authFeatures.includes('update-passwords')
) {
  routes.push({
    path: '/profile',
    name: 'auth.profile',
    component: () => import('./pages/PageProfile.vue'),
    meta: {
      middleware: 'auth',
    },
  })
}

export default routes
