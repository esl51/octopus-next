import { h, resolveComponent } from 'vue'
import { useMount } from 'vue3-mount'

const confirmator = () => {
  const mount = useMount()
  type Node = ReturnType<typeof mount>

  type ConfirmPromise = Promise<boolean> & {
    mount: Node
  }

  return (options?: {
    title?: string
    body?: string
    size?: string
    yesVariant?: string
    yesClass?: string
    yesTitle?: string
    hideOnYes?: boolean
    noVariant?: string
    noClass?: string
    noTitle?: string
    hideOnNo?: boolean
    hideOnBackdropClick?: boolean
  }): ConfirmPromise => {
    let resolveFn: (value: boolean) => void

    const promise = new Promise((resolve) => {
      resolveFn = resolve
    }) as ConfirmPromise

    const unmount = () => {
      setTimeout(() => {
        promise.mount.unmount()
      }, 1000)
    }

    promise.mount = mount(
      () =>
        h(resolveComponent('OConfirm'), {
          ...options,
          onYes: () => resolveFn(true),
          onNo: () => resolveFn(false),
          onHide: () => unmount(),
        }),
      'confirms'
    )

    return promise
  }
}

export function useConfirm() {
  const confirm = confirmator()

  return {
    confirm,
  }
}
