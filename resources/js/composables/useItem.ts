import OModal from '@/components/OModal.vue'
import { useConfirm } from '@/composables/useConfirm'
import { Item, ItemApi } from '@/types'
import { File } from '@/modules/files/types'
import { serialize } from 'object-to-formdata'
import Form from 'vform'
import { computed, nextTick, onMounted, reactive, Ref, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

interface ItemConfig<T extends Item, U extends ItemApi<T> = ItemApi<T>> {
  id?: number
  api: U
  defaults: Record<string, unknown>
  modal?: Ref<typeof OModal | null>
  fetchOnMount?: boolean
}

export function useItem<T extends Item>(config: ItemConfig<T>) {
  const { confirm } = useConfirm()
  const { t, availableLocales } = useI18n()

  const busy = ref(false)

  // fetch on mount
  const fetchOnMount = computed(() =>
    config.fetchOnMount === false ? false : true,
  )

  // form
  const form = reactive(Form.make(config.defaults))

  // init form
  const initForm = () => {
    form.clear()
    form.reset()
  }

  // fill form
  const fillForm = (item: T) => {
    initForm()
    const data = {} as Record<string, unknown>
    Object.keys(config.defaults).forEach((key) => {
      data[key] = null
      if (Array.isArray(item[key])) {
        if ((item[key] as Array<File>)?.some((f) => f.original_name && f.url)) {
          // files
          data[key] = []
        } else if ((item[key] as Array<T>)?.some((i) => i.id)) {
          // options
          data[key] = (item[key] as Array<T>)?.map((i) => i.id)
        } else {
          data[key] = []
        }
      } else if (
        (item[key] as File)?.original_name &&
        (item[key] as File)?.url
      ) {
        // file
        data[key] = null
      } else if ((item[key] as T)?.id) {
        // option
        data[key] = (item[key] as T)?.id
      } else {
        data[key] = item[key]
      }

      // translations
      const localeKeys = key.match(/^([^:]+):([^$]+)$/)
      if (
        item.translations &&
        Array.isArray(localeKeys) &&
        availableLocales.includes(localeKeys[2])
      ) {
        const localeKey = localeKeys[1]
        const locale = localeKeys[2]
        const localeTranslations = item.translations.find(
          (translation) => translation.locale === locale,
        )
        if (localeTranslations && localeTranslations[localeKey]) {
          data[key] = localeTranslations[localeKey]
        } else {
          data[key] = null
        }
      }
    })
    form.fill(data)
  }

  // current item
  const current = ref({} as T) as Ref<T>

  // fetch item
  const fetchItem = async (id: number) => {
    loadItem(await config.api.get(id))
  }

  // load item
  const loadItem = async (item: T) => {
    current.value = item
  }

  // edit
  const edit = (item: T) => {
    loadItem(item)
    fillForm(item)
    config.modal?.value?.show()
  }

  // submit
  const submit = async () => {
    if (current.value.id) {
      loadItem(await update(current.value.id))
    }
    config.modal?.value?.hide()
  }

  // transform request translations
  const transformRequest = (data: Record<string, unknown>) => {
    Object.entries(data).forEach(([key, value]) => {
      data[key] = value === '' ? null : value
    })
    return serialize(data, {
      nullsAsUndefineds: true,
      booleansAsIntegers: true,
    })
  }

  // update
  const update = async (item: number | T) => {
    const id = typeof item === 'number' ? item : item.id
    const { data } = await form.post(config.api.url + id, {
      transformRequest: [
        (data) => ({ ...data, _method: 'PUT' }),
        (data) => transformRequest(data),
      ],
    })
    return data.data
  }

  // destroy
  const destroy = async (item: number | T, name?: string) => {
    const id = typeof item === 'number' ? item : item.id
    if (
      await confirm({
        title: t('global.confirm_title'),
        body: t('global.confirm_delete_body', {
          name: name ?? id,
        }),
        yesVariant: 'danger',
        yesTitle: t('global.yes_delete'),
      })
    ) {
      await config.api.destroy(id)
    }
  }

  // focus first error
  watch(
    () => form.errors.errors,
    (errors) => {
      const firstError = Object.keys(errors)[0]
      if (firstError) {
        const input = document.querySelector(
          '[name="' + firstError + '"]',
        ) as HTMLInputElement
        if (input) {
          nextTick(() => {
            input.focus()
          })
        }
      }
    },
    { deep: true },
  )

  // fetch on mount
  onMounted(async () => {
    if (config.id && fetchOnMount.value === true) {
      fetchItem(config.id)
    }
  })

  return {
    busy,
    form,
    current,
    fetchItem,
    edit,
    update,
    submit,
    destroy,
  }
}
