import { useLangStore } from '@/stores/lang'

export function useFormatter() {
  const langStore = useLangStore()
  const formatDate = (value: Date | string) => {
    if (value) {
      return new Date(value)
        .toLocaleDateString(langStore.locale, {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
        })
        .replace(' г.', '')
    }
    return null
  }

  const formatDateTime = (value: Date | string) => {
    if (value) {
      return new Date(value)
        .toLocaleString(langStore.locale, {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: 'numeric',
          minute: 'numeric',
        })
        .replace(' г.', '')
    }
    return null
  }

  const formatTime = (value: Date | string) => {
    if (value) {
      return new Date(value)
        .toLocaleString(langStore.locale, {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: 'numeric',
          minute: 'numeric',
        })
        .replace(' г.', '')
        .split(', ')
        .slice(-1)[0]
    }
    return null
  }

  const formatDatePeriod = (start: Date | string, finish: Date | string) => {
    if (!start || !finish) {
      return null
    }
    let result = ''
    const startDate = new Date(start)
    const finishDate = new Date(finish)
    result += startDate
      .toLocaleDateString(langStore.locale, {
        year:
          startDate.getFullYear() === finishDate.getFullYear()
            ? undefined
            : 'numeric',
        month: 'short',
        day: 'numeric',
      })
      .replace(' г.', '')
    result += ' - '
    result += finishDate
      .toLocaleDateString(langStore.locale, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      })
      .replace(' г.', '')
    return result
  }

  const formatMoney = (value: number | string, currency = 'RUB') => {
    return Number(value)
      .toLocaleString(langStore.locale, { style: 'currency', currency })
      .replace(/,00/, '')
  }

  const formatFileSize = (bytes: number, decimals = 1) => {
    if (bytes == 0) {
      return '0 B'
    }
    const k = 1024,
      sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
      i = Math.floor(Math.log(bytes) / Math.log(k))
    return (
      parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i]
    )
  }

  return {
    formatDate,
    formatDateTime,
    formatTime,
    formatDatePeriod,
    formatMoney,
    formatFileSize,
  }
}
