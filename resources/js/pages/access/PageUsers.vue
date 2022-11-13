<template>
  <teleport to="#page-search">
    <o-search @input="search" />
  </teleport>
  <teleport to="#page-buttons">
    <o-button-add @click="add" />
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
      <template #cell(name)="{ item }">
        <div class="d-flex py-1 align-items-center min-w-0">
          <o-avatar
            :image="item.photo_url"
            :placeholder="item.name_placeholder"
            class="flex-shrink-0 me-2"
          />
          <div class="flex-fill min-w-0">
            <div class="font-weight-medium text-truncate">{{ item.name }}</div>
            <div class="text-muted fw-normal text-truncate">
              <a
                :href="'mailto:' + item.email"
                class="text-reset"
              >
                {{ item.email }}
              </a>
            </div>
          </div>
        </div>
      </template>
      <template #cell(roles)="{ item }">
        <div v-html="item.roles.map((r: Item) => r.title).join('<br />')" />
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
            @click="destroy(item, item.name)"
          >
            <trash-icon class="icon dropdown-item-icon text-danger" />
            {{ $t('global.delete') }}
          </b-dropdown-item-button>
        </b-dropdown>
      </template>
    </o-table>
    <template #footer>
      <o-table-footer
        :meta="meta"
        @paginate="paginate"
      />
    </template>
  </b-card>
  <o-modal
    ref="modal"
    :title="current.name as string ?? $t('access.users.new_title')"
    @hide="cleanRoute"
  >
    <v-form
      :form="form"
      @submit="submit"
    >
      <v-input
        name="name"
        :label="$t('access.users.name_label')"
        autofocus
      />
      <v-input
        name="email"
        :label="$t('access.users.email_label')"
        type="email"
      />
      <v-input
        name="password"
        :label="$t('access.users.password_label')"
        type="password"
        autocomplete="new-password"
      />
      <v-checkboxes
        name="roles"
        :label="$t('access.roles.title')"
        :options="roles"
        label-attribute="title"
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
import { rolesApi, usersApi } from '@/api/access'
import { Item } from '@/api/items'
import OModal from '@/components/OModal.vue'
import { useItems } from '@/composables/useItems'
import { usePage } from '@/composables/usePage'
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// page
usePage({
  title: t('access.users.title'),
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
  email: '',
  roles: [],
} as Record<string, unknown>

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
  api: usersApi,
  defaults,
  modal,
  params: {
    per_page: 6,
  },
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
    key: 'name',
    title: t('access.users.name_label'),
    sortable: true,
    class: 'table-title-column',
  },
  {
    key: 'roles',
    title: t('access.roles.title'),
    sortable: false,
  },
  {
    key: 'action-column',
    class: 'table-action-column',
  },
])

// roles
const roles = ref<Array<Item>>([])
onMounted(async () => {
  const { data } = await rolesApi.all()
  roles.value = data
})
</script>
