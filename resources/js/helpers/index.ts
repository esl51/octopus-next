import dayjs from 'dayjs'

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

/**
 * Cast strings as Date
 * @param body Object
 * @param dateTimeFormat Date time format for dayjs
 * @returns void
 */
export function handleDates(
  body: any, // eslint-disable-line @typescript-eslint/no-explicit-any
  dateTimeFormat = 'YYYY-MM-DD[T]HH:mm:ssZ'
) {
  if (body === null || body === undefined || typeof body !== 'object') {
    return body
  }
  for (const key of Object.keys(body)) {
    const value = body[key]
    const date = dayjs(value, dateTimeFormat, true)
    if (date.format(dateTimeFormat) === value) {
      body[key] = date.toDate()
    } else if (typeof value === 'object') {
      handleDates(value)
    }
  }
}
