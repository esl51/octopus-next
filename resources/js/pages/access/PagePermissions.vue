<template>
  <teleport to="#page-search">
    <o-search @input="search" />
  </teleport>
  <teleport to="#page-buttons">
    <o-button
      v-b-tooltip="$t('global.add')"
      variant="primary"
      icon
      @click="add"
    >
      <plus-icon class="icon" />
    </o-button>
  </teleport>
  <div
    ref="card"
    class="card"
    :class="cardClasses"
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
          variant="link"
          no-caret
          toggle-class="p-0 btn-action"
          :popper-opts="{ strategy: 'fixed' }"
        >
          <template #button-content>
            <dots-vertical-icon class="icon" />
          </template>
          <b-dropdown-item-button
            :disable="!item.is_editable"
            @click="edit(item)"
          >
            <edit-icon
              class="icon dropdown-item-icon"
              :disable="!item.is_deletable"
            />
            {{ $t('global.edit') }}
          </b-dropdown-item-button>
          <b-dropdown-item-button
            variant="danger"
            @click="destroy(item, item.name)"
          >
            <trash-icon class="icon dropdown-item-icon text-danger" />
            {{ $t('global.delete') }}
          </b-dropdown-item-button>
        </b-dropdown>
      </template>
    </o-table>
    <div class="card-footer d-flex align-items-center">
      <p class="m-0 text-muted">
        {{ $t('global.items_table_footer', { ...meta }) }}
      </p>
      <o-pagination
        class="m-0 ms-auto"
        :meta="meta"
        @change="paginate"
      />
    </div>
  </div>
  <o-modal
    ref="modal"
    :title="current.name as string ?? $t('access.permissions.new_title')"
  >
    <v-form
      :form="form"
      @submit="submit"
    >
      <v-input
        v-model="form.name"
        name="name"
        :label="$t('global.name_label')"
        autofocus
      />
      <v-input
        v-model="form.guard_name"
        name="guard_name"
        :label="$t('access.guard_name_label')"
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
import { permissionsApi } from '@/api/access'
import OModal from '@/components/OModal.vue'
import { useItems } from '@/composables/useItems'
import { usePage } from '@/composables/usePage'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// page
usePage({
  title: t('access.permissions.title'),
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
}

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
} = useItems({
  api: permissionsApi,
  defaults,
  modal,
})

// card class
const card = ref(null)
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
    class: 'w-1',
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
    class: 'w-1',
  },
])
</script>
