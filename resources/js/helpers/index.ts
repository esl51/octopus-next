import dayjs from 'dayjs'
import timezone from 'dayjs/plugin/timezone'

dayjs.extend(timezone)

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
 * @returns void
 */
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export function handleDates(body: any) {
  if (body === null || body === undefined || typeof body !== 'object') {
    return body
  }
  for (const key of Object.keys(body)) {
    const value = body[key]
    if (typeof value === 'string') {
      const tz = import.meta.env.VITE_SERVER_TIMEZONE
      const date = dayjs(value).tz(tz)
      if (date.format().replace(/Z$/, '+00:00') === value.toString()) {
        body[key] = new Date(
          date.toDate().toLocaleString('en-US', { timeZone: tz })
        )
      } else {
        body[key] = value
      }
    } else if (typeof value === 'object') {
      handleDates(value)
    }
  }
}

/**
 * Cast Dates as strings
 * @param body Object
 * @returns void
 */
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export function handleDatesAsStrings(body: any) {
  if (body === null || body === undefined || typeof body !== 'object') {
    return body
  }
  for (const key of Object.keys(body)) {
    const value = body[key]
    if (value instanceof Date) {
      body[key] = serializeDate(value)
    } else if (typeof value === 'object') {
      handleDatesAsStrings(value)
    }
  }
}

/**
 * Serialize Date as string
 * @param date Date
 * @returns string
 */
export function serializeDate(date: Date): string {
  return dayjs(date).format('YYYY-MM-DDTHH:mm:ss')
}
