import { MiddlewareContext, MiddlewareInterface } from '@/types'

export default async function middlewarePipeline(
  context: MiddlewareContext,
  middleware: Array<(data: MiddlewareInterface) => void>,
  index: number,
): Promise<void> {
  const nextMiddleware = middleware[index]
  if (!nextMiddleware) {
    return context.next()
  }
  const next = async () => {
    await middlewarePipeline(context, middleware, index + 1)
  }
  await nextMiddleware({
    ...context,
    next,
  })
}
