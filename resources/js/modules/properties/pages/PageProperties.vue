<template>
  <o-page-header
    :title="id ? (singleItem?.name as string) : $t('properties.title')"
  >
    <template
      v-if="!id"
      #buttons
    >
      <o-search v-model="params.search" />
      <o-button-add @click="add" />
    </template>
    <template
      v-if="!id && meta.total > 1"
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
      @sort="sort"
    >
      <template #cell(name)="{ item }">
        <router-link
          v-if="item.property_type.has_values"
          :to="{ name: 'directory.property', params: { id: item.id } }"
        >
          {{ item.name }}
        </router-link>
        <template v-else>
          {{ item.name }}
        </template>
      </template>
      <template #cell(property_type_id)="{ item }">
        {{ item.property_type?.name }}
      </template>
      <template #cell(property_group_id)="{ item }">
        {{ item.property_group?.name }}
      </template>
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
    :title="(current.name as string) ?? $t('properties.new_title')"
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
      <v-input
        name="alias"
        :label="$t('properties.alias_label')"
      />
      <v-select
        v-if="!current.id"
        name="property_type_id"
        :label="$t('properties.property_type_label')"
        :options="propertyTypes"
      />
      <v-select
        name="property_group_id"
        :label="$t('properties.property_group_label')"
        :options="propertyGroups"
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
import { propertiesApi, propertyGroupsApi, propertyTypesList } from '../api'
import OModal from '@/components/OModal.vue'
import { useItems } from '@/composables/useItems'
import { usePage } from '@/composables/usePage'
import { Item } from '@/types'
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { Property } from '../types'

const props = defineProps<{
  id?: number
}>()

const singleItem = computed(() => items.value.find((i) => i.id === props.id))

const { t, availableLocales } = useI18n()

// page
usePage({
  title: t('properties.title'),
})

// modal
const modal = ref<typeof OModal | null>(null)

// defaults
const defaults = {
  alias: '',
  property_type_id: '',
  property_group_id: '',
} as Record<string, unknown>
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
  add,
  edit,
  submit,
  destroy,
  cleanRoute,
} = useItems<Property>({
  api: propertiesApi,
  defaults,
  modal,
  params: {
    id: props.id,
  },
  listKey: 'properties',
})

// columns
const columns = ref([
  {
    key: 'id',
    title: t('global.id_label'),
    sortable: !props.id,
    class: 'table-id-column',
  },
  {
    key: 'name',
    title: t('global.name_label'),
    sortable: !props.id,
    class: 'table-title-column',
    disabled: !!props.id,
  },
  {
    key: 'alias',
    title: t('properties.alias_label'),
    sortable: !props.id,
  },
  {
    key: 'property_type_id',
    title: t('properties.property_type_label'),
    sortable: !props.id,
  },
  {
    key: 'property_group_id',
    title: t('properties.property_group_label'),
    sortable: !props.id,
  },
  {
    key: 'action-column',
    class: 'table-action-column',
  },
])

// propertyGroups
const propertyGroups = ref<Array<Item>>([])
onMounted(async () => {
  const { data } = await propertyGroupsApi.all()
  propertyGroups.value = data
})

// propertyTypes
const propertyTypes = ref<Array<Item>>([])
onMounted(async () => {
  propertyTypes.value = await propertyTypesList()
})
</script>
