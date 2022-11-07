import { MiddlewareInterface } from '@/types'
import { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'

declare interface Context {
  to: RouteLocationNormalized
  from: RouteLocationNormalized
  next: NavigationGuardNext
}

export default function middlewarePipeline(
  context: Context,
  middleware: Array<(data: MiddlewareInterface) => void>,
  index: number
): NavigationGuardNext /* | Promise<Context> */ {
  const nextMiddleware = middleware[index]
  if (!nextMiddleware) {
    return context.next
  }
  return () => {
    nextMiddleware({
      ...context,
      next: middlewarePipeline(context, middleware, index + 1),
    })
  }
}
