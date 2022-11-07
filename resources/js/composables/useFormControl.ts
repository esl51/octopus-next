import Form from 'vform'
import { computed, getCurrentInstance, inject, provide } from 'vue'

interface ControlConfig {
  name: string
  hint?: string
  size?: string
  label?: string
}

export function useFormControl(config: ControlConfig) {
  const { name, hint, size, label } = config

  // form
  const form: Form | undefined = inject('form')

  // unique id
  const id = computed(() => 'input-' + getCurrentInstance()?.uid)

  // validation
  const errors = computed(() => form?.value.errors?.get(name))
  const state = computed(() =>
    form?.value.errors?.has(name) ? false : undefined
  )

  provide('id', id)
  provide('name', name)
  provide('hint', hint)
  provide('size', size)
  provide('label', label)
  provide('state', state)
  provide('errors', errors)

  return {
    id,
    errors,
    state,
  }
}
