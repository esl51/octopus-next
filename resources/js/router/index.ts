import routes from './routes'
import { createWebHistory, createRouter } from 'vue-router'

const globalMiddleware = ['locale']

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from) => {
  const context = { to, from }
  const middleware = [...globalMiddleware]
  const metaMiddleware = to.meta?.middleware

  if (metaMiddleware) {
    if (Array.isArray(metaMiddleware)) {
      middleware.push(...metaMiddleware)
    } else {
      middleware.push(metaMiddleware)
    }
  }

  if (middleware.length) {
    for (const name of middleware) {
      const module = await import(`../middleware/${name}.ts`)
      const result = await module.default(context)

      if (
        result &&
        ((typeof result === 'object' && 'name' in result) ||
          typeof result == 'string')
      ) {
        return result
      }
    }
  }
})

export default router
