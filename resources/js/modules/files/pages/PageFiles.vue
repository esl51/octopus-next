<template>
  <teleport to="#page-search">
    <o-search @input="search" />
  </teleport>
  <teleport to="#page-meta">
    <span v-if="meta.total > 1">
      {{ $t('global.page_meta', { ...meta }) }}
    </span>
  </teleport>
  <b-card
    :class="cardClasses"
    body-class="p-0"
    footer-class="border-top-0"
  >
    <o-table
      class="card-table"
      :data="items"
      :params="params"
      :columns="columns"
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
        <b-dropdown
          v-if="item.is_editable || item.is_deletable"
          variant="link"
          no-caret
          toggle-class="btn-action btn-action-table"
          :popper-opts="{ strategy: 'fixed' }"
        >
          <template #button-content>
            <dots-vertical-icon class="icon" />
          </template>
          <b-dropdown-item-button
            :disabled="!item.is_editable"
            @click="edit(item)"
          >
            <edit-icon class="icon dropdown-item-icon" />
            {{ $t('global.edit') }}
          </b-dropdown-item-button>
          <b-dropdown-item-button
            variant="danger"
            :disabled="!item.is_deletable"
            @click="destroy(item, item.title)"
          >
            <trash-icon class="icon dropdown-item-icon text-danger" />
            {{ $t('global.delete') }}
          </b-dropdown-item-button>
        </b-dropdown>
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

// card class
const cardClasses = computed(() => ({
  'opacity-50': busy.value,
  'pe-none': busy.value,
}))

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