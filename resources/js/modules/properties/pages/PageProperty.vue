<template>
  <page-properties :id="Number($route.params.id)" />
  <o-page-header
    :title="$t('properties.property_values.title')"
    title-tag="h2"
  >
    <template #buttons>
      <o-search v-model="params.search" />
      <o-button-add @click="add" />
    </template>
    <template
      v-if="meta.total > 1"
      #meta
    >
      {{ $t('global.page_meta', { ...meta }) }}
    </template>
  </o-page-header>
  <o-card
    body-class="p-0"
    footer-class="border-top-0"
  >
    <o-table
      :data="items"
      :params="params"
      :columns="columns"
      :busy="busy"
      card
      sortable
      @move="move"
      @sort="sort"
    >
      <template #cell(action-column)="{ item }">
        <o-actions
          v-if="item.is_deletable || item.is_editable"
          :item="item"
          @edit="edit(item)"
          @delete="destroy(item, item.name)"
        />
      </template>
    </o-table>
    <template #footer>
      <o-table-footer
        v-if="meta.last_page > 1"
        :meta="meta"
        @paginate="paginate"
      />
    </template>
  </o-card>
  <o-modal
    ref="modal"
    :title="
      (current.name as string) ?? $t('properties.property_values.new_title')
    "
    @hide="cleanRoute"
  >
    <v-form
      :form="form"
      @submit="submit"
    >
      <v-input
        name="name"
        :label="$t('global.name_label')"
        autofocus
        translatable
      />
      <template #footer>
        <o-button
          variant="link"
          class="link-secondary me-2"
          @click="modal?.hide()"
        >
          {{ $t('global.cancel') }}
        </o-button>
        <v-submit class="ms-auto">
          {{ $t('global.save') }}
        </v-submit>
      </template>
    </v-form>
  </o-modal>
</template>

<script setup lang="ts">
import { propertiesApi, propertiesUrls } from '../api'
import PageProperties from './PageProperties.vue'
import itemsApi from '@/api/items'
import OModal from '@/components/OModal.vue'
import { useItems } from '@/composables/useItems'
import { usePage } from '@/composables/usePage'
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { PropertyValue } from '../types'

const { t, availableLocales } = useI18n()
const route = useRoute()
const router = useRouter()

// property
const property = ref()
onMounted(async () => {
  await router.isReady()
  property.value = await propertiesApi.get(Number(route.params.id))

  // page
  usePage({
    title: property.value?.name,
  })
})

// modal
const modal = ref<typeof OModal | null>(null)

// defaults
const defaults = {} as Record<string, unknown>
availableLocales.forEach((locale) => {
  defaults['name:' + locale] = ''
})

// items
const {
  items,
  meta,
  params,
  busy,
  form,
  current,
  paginate,
  sort,
  move,
  add,
  edit,
  submit,
  destroy,
  cleanRoute,
} = useItems({
  api: itemsApi<PropertyValue>(
    propertiesUrls.properties + '/' + route.params.id + '/values',
  ),
  defaults,
  modal,
  listKey: 'property-' + route.params.id + '-values',
})

// columns
const columns = ref([
  {
    key: 'id',
    title: t('global.id_label'),
    sortable: true,
    class: 'table-id-column',
  },
  {
    key: 'name',
    title: t('global.name_label'),
    sortable: true,
    class: 'table-title-column',
  },
  {
    key: 'action-column',
    class: 'table-action-column',
  },
])
</script>
