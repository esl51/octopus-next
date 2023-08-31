import { h, resolveComponent } from 'vue'
import { useMount } from 'vue3-mount'

interface ToastOptions {
  variant?: string
  timeout?: number
  title?: string
  body?: string
  hint?: string
}

const toaster = () => {
  const mount = useMount()

  return (options?: ToastOptions): ReturnType<typeof mount> => {
    const unmount = () => {
      setTimeout(() => {
        mounted.unmount()
      }, 1000)
    }

    const mounted = mount(
      () =>
        h(resolveComponent('OToast'), {
          ...options,
          onHide: () => unmount(),
        }),
      'toasts',
    )

    return mounted
  }
}

export function useToast() {
  const toast = (options?: ToastOptions) => {
    return toaster()(options)
  }

  const danger = (options?: ToastOptions) => {
    const settings = options ?? ({} as ToastOptions)
    settings.variant = 'danger'
    return toaster()(settings)
  }

  const warning = (options?: ToastOptions) => {
    const settings = options ?? ({} as ToastOptions)
    settings.variant = 'warning'
    return toaster()(settings)
  }

  const info = (options?: ToastOptions) => {
    const settings = options ?? ({} as ToastOptions)
    settings.variant = 'info'
    return toaster()(settings)
  }

  const success = (options?: ToastOptions) => {
    const settings = options ?? ({} as ToastOptions)
    settings.variant = 'success'
    return toaster()(settings)
  }

  return {
    toast,
    danger,
    warning,
    info,
    success,
  }
}
