import OModal from '@/components/OModal.vue'
import { useConfirm } from '@/composables/useConfirm'
import { debounce } from '@/helpers'
import { Item, ItemsApi, ListParams, Meta } from '@/types'
import { File } from '@/modules/files/types'
import { serialize } from 'object-to-formdata'
import Form from 'vform'
import { computed, nextTick, onMounted, reactive, Ref, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { useListsStore } from '@/stores/lists'

interface ItemsConfig<T extends Item, U extends ItemsApi<T> = ItemsApi<T>> {
  api: U
  defaults: Record<string, unknown>
  modal?: Ref<typeof OModal | null>
  params?: ListParams
  fetchOnMount?: boolean
  listKey: string
}

export function useItems<T extends Item>(config: ItemsConfig<T>) {
  const router = useRouter()
  const route = useRoute()
  const { confirm } = useConfirm()
  const { t, availableLocales } = useI18n()

  const items = ref([] as Array<T>) as Ref<Array<T>>
  const meta = ref({} as Meta)
  const params = ref({
    search: '',
    per_page: 15,
    page: 1,
    sort_by: '',
    sort_desc: 0,
    ...config.params,
  } as ListParams)

  const busy = ref(false)

  // lists
  const lists = useListsStore()

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

  // set items
  const setItems = (newItems: Array<T>) => {
    items.value = newItems
  }

  // fetch items
  const fetchItems = async () => {
    busy.value = true
    try {
      const response = await config.api.list({ ...params.value })
      items.value = response.data
      meta.value = response.meta
      if (params.value.page && params.value.page > meta.value.last_page) {
        paginate(meta.value.last_page)
      }
    } finally {
      busy.value = false
    }
  }

  // paginate
  const paginate = (page: number) => {
    lists.update(config.listKey, {
      ...params.value,
      page,
    })
  }

  // sort
  const sort = (key: string) => {
    let sortBy = key
    let sortDesc = params.value.sort_desc
    if (params.value.sort_by == sortBy) {
      if (sortDesc == 0) {
        sortDesc = 1
      } else if (sortDesc == 1) {
        sortDesc = 0
        sortBy = ''
      }
    } else {
      sortDesc = 0
    }
    lists.update(config.listKey, {
      ...params.value,
      sort_by: sortBy,
      sort_desc: sortDesc,
    })
  }

  // move
  interface MoveEvent extends Event {
    oldIndex: number
    newIndex: number
  }
  const move = async (event: MoveEvent) => {
    try {
      loadItem(items.value[event.oldIndex])
      items.value.splice(event.oldIndex, 1)
      items.value.splice(event.newIndex, 0, current.value)
      if (event.newIndex > event.oldIndex) {
        loadItem(
          await config.api.moveAfter(
            current.value.id,
            items.value[event.newIndex - 1].id,
          ),
        )
      } else if (event.newIndex < event.oldIndex) {
        loadItem(
          await config.api.moveBefore(
            current.value.id,
            items.value[event.newIndex + 1].id,
          ),
        )
      }
    } catch (e) {
      items.value = []
    }
    if (event.oldIndex !== event.newIndex) {
      fetchItems()
    }
  }

  // search
  const search = debounce((query: string) => {
    lists.update(config.listKey, {
      ...params.value,
      search: query,
      page: 1,
    })
  }, 300)
  watch(
    () => params.value.search as string,
    async (query) => {
      search(query)
    },
  )

  // add
  const add = () => {
    loadItem({} as T)
    initForm()
    config.modal?.value?.show()
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
    } else {
      loadItem(await store())
    }
    fetchItems()
    config.modal?.value?.hide()
  }

  // transform request translations
  const transformRequest = (data: Record<string, unknown>) => {
    Object.entries(data).forEach(([key, value]) => {
      data[key] = value === '' ? null : value
    })
    return serialize(data, { nullsAsUndefineds: true })
  }

  // store
  const store = async () => {
    const { data } = await form.post(config.api.url, { transformRequest })
    return data.data
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
      fetchItems()
    }
  }

  // clean route add/edit params to prevent showing modal when route changes
  const cleanRoute = () => {
    if (route.query.add || route.query.edit) {
      const query = { ...route.query, add: undefined, edit: undefined }
      router.replace({ query })
    }
  }

  // route change
  watch(
    () => route.query,
    async (query) => {
      const clearParams = { ...params.value }
      if (!query.add && clearParams.add) {
        delete clearParams.add
      }
      if (!query.edit && clearParams.edit) {
        delete clearParams.edit
      }
      params.value = {
        ...clearParams,
        ...lists.lists[config.listKey],
      }
      fetchItems()
    },
    { deep: true },
  )

  // list change
  watch(
    () => lists.lists[config.listKey],
    async (listParams) => {
      params.value = {
        ...params.value,
        ...listParams,
      }
      fetchItems()
    },
    { deep: true },
  )

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

  // show add/edit modal with specific route query
  onMounted(async () => {
    if (fetchOnMount.value === true) {
      fetchItems()
    }
    const addItem = Number(route.query.add)
    const editItem = Number(route.query.edit)
    if (addItem) {
      nextTick(() => {
        add()
      })
    } else if (editItem) {
      await fetchItem(editItem)
      nextTick(() => {
        edit(current.value)
      })
    }
  })

  return {
    items,
    meta,
    params,
    busy,
    form,
    current,
    fetchItem,
    setItems,
    fetchItems,
    paginate,
    sort,
    move,
    search,
    add,
    store,
    edit,
    update,
    submit,
    destroy,
    cleanRoute,
  }
}
