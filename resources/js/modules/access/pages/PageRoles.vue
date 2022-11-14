<template>
  <teleport to="#page-search">
    <o-search @input="search" />
  </teleport>
  <teleport to="#page-buttons">
    <o-button-add @click="add" />
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
    :title="current.title as string ?? $t('access.roles.new_title')"
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
      />
      <v-input
        name="title"
        :label="$t('global.title_label')"
        translatable
      />
      <v-input
        name="guard_name"
        :label="$t('access.guard_name_label')"
        disabled
      />
      <v-checkboxes
        name="permissions"
        :label="$t('access.permissions.title')"
        :options="permissions"
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
import { permissionsApi, rolesApi } from '../api'
import { Item } from '@/api/items'
import OModal from '@/components/OModal.vue'
import { useItems } from '@/composables/useItems'
import { usePage } from '@/composables/usePage'
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, availableLocales } = useI18n()

// page
usePage({
  title: t('access.roles.title'),
  breadcrumb: [
    {
      text: t('access.title'),
      to: { name: 'access.users' },
    },
  ],
})

// modal
const modal = ref<typeof OModal | null>(null)

// defaults
const defaults = {
  name: '',
  guard_name: 'web',
  permissions: [],
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
  add,
  edit,
  submit,
  destroy,
  cleanRoute,
} = useItems({
  api: rolesApi,
  defaults,
  modal,
})

// card class
const cardClasses = computed(() => ({
  'opacity-50': busy.value,
  'pe-none': busy.value,
}))

// columns
const columns = ref([
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
    key: 'name',
    title: t('global.name_label'),
    sortable: true,
  },
  {
    key: 'guard_name',
    title: t('access.guard_name_label'),
    sortable: true,
  },
  {
    key: 'action-column',
    class: 'table-action-column',
  },
])

// permissions
const permissions = ref<Array<Item>>([])
onMounted(async () => {
  const { data } = await permissionsApi.all()
  permissions.value = data
})
</script>