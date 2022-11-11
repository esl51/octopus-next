/**
 * Debounce
 * @param func Callback function
 * @param waitFor Delay
 * @returns void
 */
export function debounce<F extends (...args: Parameters<F>) => ReturnType<F>>(
  func: F,
  waitFor: number
): (...args: Parameters<F>) => void {
  let timeout: ReturnType<typeof setTimeout>
  return (...args: Parameters<F>): void => {
    clearTimeout(timeout)
    timeout = setTimeout(() => func(...args), waitFor)
  }
}

/**
 * Add namespace to params object
 * @param params Source params
 * @param namespace Namespace
 * @returns Namespaced params
 */
export function namespaceParams(
  params: Record<string, unknown>,
  namespace: string
): Record<string, unknown> {
  const nsParams = {} as Record<string, unknown>
  Object.entries(params).forEach(([key, value]) => {
    nsParams[namespace + '_' + key] = value
  })
  return nsParams
}

/**
 * Delete namespace from params object
 * @param params Namespaced params
 * @param namespace Namespace
 * @returns Params
 */
export function simplifyParams(
  params: Record<string, unknown>,
  namespace: string
): Record<string, unknown> {
  const sParams = {} as Record<string, unknown>
  Object.entries(params).forEach(([key, value]) => {
    if (key.startsWith(namespace + '_')) {
      sParams[key.replace(namespace + '_', '')] = value
    }
  })
  return sParams
}
