import middlewarePipeline from './middlewarePipeline'
import routes from './routes'
import { MiddlewareInterface } from '@/types'
import { createWebHistory, createRouter } from 'vue-router'

const globalMiddleware = ['locale']

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const context = { to, from, next }
  const middleware = [...globalMiddleware]
  const metaMiddleware = to.meta?.middleware

  if (metaMiddleware) {
    if (Array.isArray(metaMiddleware)) {
      middleware.push(...metaMiddleware)
    } else {
      middleware.push(metaMiddleware)
    }
  }

  if (!middleware.length) {
    return next()
  }

  const middlewareModules: Array<
    ({ to, from, next }: MiddlewareInterface) => void
  > = []
  for (const name of middleware) {
    const module = await import(/* @vite-ignore */ `../middleware/${name}.ts`)
    middlewareModules.push(module.default)
  }

  middlewareModules[0]({
    ...context,
    next: middlewarePipeline(context, middlewareModules, 1),
  })
})

export default router
