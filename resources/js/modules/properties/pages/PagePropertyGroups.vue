<template>
  <o-page-header :title="$t('properties.property_groups.title')">
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
      (current.name as string) ?? $t('properties.property_groups.new_title')
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
import { propertyGroupsApi } from '../api'
import OModal from '@/components/OModal.vue'
import { useItems } from '@/composables/useItems'
import { usePage } from '@/composables/usePage'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { PropertyGroup } from '../types'

const { t, availableLocales } = useI18n()

// page
usePage({
  title: t('properties.property_groups.title'),
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
} = useItems<PropertyGroup>({
  api: propertyGroupsApi,
  defaults,
  modal,
  listKey: 'property-groups',
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
