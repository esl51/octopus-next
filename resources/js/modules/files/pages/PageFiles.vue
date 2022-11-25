<template>
  <o-page-header :title="$t('access.permissions.title')">
    <template #buttons>
      <o-search @input="search" />
    </template>
    <template
      v-if="meta.total > 1"
      #meta
    >
      {{ $t('global.page_meta', { ...meta }) }}
    </template>
  </o-page-header>
  <b-card
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
      <template #cell(title)="{ item }">
        <div class="d-flex py-1 align-items-center min-w-0">
          <o-file-image
            :file="item"
            class="flex-shrink-0 me-2"
          />
          <div class="flex-fill min-w-0">
            <div class="font-weight-medium text-truncate">{{ item.title }}</div>
          </div>
        </div>
      </template>
      <template #cell(action-column)="{ item }">
        <o-actions
          v-if="item.is_deletable || item.is_editable"
          :item="item"
          @edit="edit(item)"
          @delete="destroy(item, item.title)"
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
  </b-card>
  <o-modal
    ref="modal"
    :title="current.title as string ?? $t('files.new_title')"
    @hide="cleanRoute"
  >
    <v-form
      :form="form"
      @submit="submit"
    >
      <v-input
        name="title"
        :label="$t('global.title_label')"
        translatable
      />
      <v-input
        name="original_name"
        :label="$t('files.original_name')"
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
import { filesApi } from '../api'
import OModal from '@/components/OModal.vue'
import { useItems } from '@/composables/useItems'
import { usePage } from '@/composables/usePage'
import { OTableColumn } from '@/types'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, availableLocales } = useI18n()

// page
usePage({
  title: t('files.title'),
})

// modal
const modal = ref<typeof OModal | null>(null)

// defaults
const defaults = {
  original_name: '',
} as Record<string, unknown>
availableLocales.forEach((locale) => {
  defaults['title:' + locale] = ''
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
  search,
  edit,
  submit,
  destroy,
  cleanRoute,
} = useItems({
  api: filesApi,
  defaults,
  modal,
})

// columns
const columns = ref<Array<OTableColumn>>([
  {
    key: 'id',
    title: t('global.id_label'),
    sortable: true,
    class: 'table-id-column',
  },
  {
    key: 'title',
    title: t('global.title_label'),
    sortable: true,
    class: 'table-title-column',
  },
  {
    key: 'original_name',
    title: t('files.original_name'),
    sortable: true,
  },
  {
    key: 'size',
    title: t('files.size'),
    sortable: true,
    formatter: 'filesize',
  },
  {
    key: 'created_at',
    title: t('files.uploaded_at'),
    sortable: true,
    formatter: 'datetime',
  },
  {
    key: 'action-column',
    class: 'table-action-column',
  },
])
</script>
