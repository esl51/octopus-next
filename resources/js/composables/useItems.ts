import { Item, ItemsApi, ListParams, Meta } from '@/api/items'
import OModal from '@/components/OModal.vue'
import { useConfirm } from '@/composables/useConfirm'
import { debounce } from '@/helpers'
import Form from 'vform'
import { nextTick, onMounted, reactive, Ref, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'

interface ItemsConfig {
  api: ItemsApi
  defaults: Record<string, unknown>
  modal?: Ref<typeof OModal | null>
  params?: ListParams
}

export function useItems(config: ItemsConfig) {
  const router = useRouter()
  const route = useRoute()
  const { confirm } = useConfirm()
  const { t } = useI18n()

  const items = ref([] as Array<Item>)
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

  // form
  const form = reactive(new Form(config.defaults))

  // fetch item
  const fetchItem = async (id: number) => {
    current.value = await config.api.get(id)
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
    router.replace({
      query: {
        ...route.query,
        ...params.value,
        page,
      },
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
    router.replace({
      query: {
        ...route.query,
        ...params.value,
        sort_by: sortBy,
        sort_desc: sortDesc,
      },
    })
  }

  // search
  const search = debounce((query: string) => {
    router.replace({
      query: {
        ...route.query,
        ...params.value,
        search: query,
        page: 1,
      },
    })
  }, 300)

  // current item
  const current = ref({} as Item)

  // add
  const add = () => {
    current.value = {} as Item
    form.fill(config.defaults)
    config.modal?.value?.show()
  }

  // edit
  const edit = (item: Item) => {
    current.value = item
    form.fill(item)
    config.modal?.value?.show()
  }

  // submit
  const submit = async () => {
    if (current.value.id) {
      current.value = await update(current.value.id)
    } else {
      current.value = await store()
    }
    fetchItems()
    config.modal?.value?.hide()
  }

  // store
  const store = async () => {
    const { data } = await form.post(config.api.url)
    return data.data
  }

  // update
  const update = async (item: number | Item) => {
    const id = typeof item === 'number' ? item : item.id
    const { data } = await form.put(config.api.url + id)
    return data.data
  }

  // destroy
  const destroy = async (item: number | Item, name?: string) => {
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
      params.value = { ...clearParams, ...query }
      fetchItems()
    },
    { immediate: true, deep: true }
  )

  // show add/edit modal with specific route query
  onMounted(async () => {
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
    fetchItems,
    paginate,
    sort,
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
