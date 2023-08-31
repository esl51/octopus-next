import Form from 'vform'
import {
  computed,
  getCurrentInstance,
  inject,
  onMounted,
  provide,
  ref,
  watch,
} from 'vue'
import { useI18n } from 'vue-i18n'

interface ControlConfig {
  name: string
  hint?: string
  size?: string
  label?: string
  translatable?: boolean
}

export function useFormControl(config: ControlConfig) {
  const { name, hint, size, label, translatable } = config

  // form
  const form: Form | undefined = inject('form')

  // unique id
  const id = computed(() => 'input-' + getCurrentInstance()?.uid)

  // locales
  const { availableLocales, fallbackLocale } = useI18n()
  const activeLocale = ref('')
  onMounted(() => {
    activeLocale.value = fallbackLocale.value as string
  })

  // validation
  const fields = computed(() =>
    translatable ? availableLocales.map((item) => name + ':' + item) : [name],
  )

  const errors = computed(() => form?.value.errors.only(...fields.value))
  const state = computed(() =>
    form?.value.errors.hasAny(...fields.value) ? false : undefined,
  )
  const localeStates = computed(() => {
    const states = {} as Record<string, boolean | undefined>
    availableLocales.forEach((locale) => {
      states[locale] = form?.value.errors.has(name + ':' + locale)
        ? false
        : undefined
    })
    return states
  })

  // change active locale to error locale
  watch(
    () => localeStates.value,
    (states) => {
      const errorLocale = Object.keys(states).find((k) => states[k] === false)
      if (errorLocale) {
        activeLocale.value = errorLocale
      }
    },
    { deep: true },
  )

  // provide data for v-form-control component
  provide('id', id)
  provide('name', name)
  provide('hint', hint)
  provide('size', size)
  provide('label', label)
  provide('state', state)
  provide('localeStates', localeStates)
  provide('errors', errors)
  provide('translatable', translatable)

  return {
    id,
    errors,
    state,
    availableLocales,
    activeLocale,
    localeStates,
  }
}
