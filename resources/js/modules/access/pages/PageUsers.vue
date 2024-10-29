<template>
  <o-page-header :title="$t('access.users.title')">
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
      @sort="sort"
    >
      <template #cell(name)="{ item }">
        <div class="d-flex py-1 align-items-center min-w-0">
          <o-avatar
            :image="item.avatar?.url"
            :placeholder="item.name_placeholder"
            class="flex-shrink-0 me-2"
          >
            <span
              v-if="item?.disabled_at"
              class="badge bg-danger"
            />
            <span
              v-else-if="
                $appConfig.authFeatures.includes('email-verification') &&
                item?.email_verified_at === null
              "
              class="badge bg-warning"
            />
          </o-avatar>
          <div class="flex-fill min-w-0">
            <div class="font-weight-medium text-truncate">{{ item.name }}</div>
            <div class="text-muted fw-normal text-truncate">
              <!-- eslint-disable sonarjs/no-vue-bypass-sanitization -->
              <a
                :href="sanitizeUrl('mailto:' + item.email)"
                class="text-reset"
              >
                {{ item.email }}
              </a>
              <!-- eslint-enable -->
            </div>
          </div>
        </div>
      </template>
      <template #cell(roles)="{ item }">
        <div
          v-dompurify-html="
            item.roles?.map((r: Item) => r.title).join('<br />')
          "
          class="text-muted"
        />
      </template>
      <template #cell(action-column)="{ item }">
        <o-actions
          v-if="
            item.is_deletable ||
            item.is_editable ||
            item.is_disablable ||
            item.is_enablable
          "
          :item="item"
          :actions="
            [
              {
                label: $t('access.users.disable_label'),
                icon: IconSquare,
                hidden: !item.is_disablable,
                variant: 'warning',
                handler() {
                  disable(item)
                },
              },
              {
                label: $t('access.users.enable_label'),
                icon: IconSquareCheck,
                hidden: !item.is_enablable,
                variant: 'success',
                handler() {
                  enable(item)
                },
              },
            ] as Array<ItemAction>
          "
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
    :title="(current.name as string) ?? $t('access.users.new_title')"
    @hide="cleanRoute"
  >
    <v-form
      :form="form"
      @submit="submitUser"
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
      <v-file
        name="avatar"
        :label="$t('access.users.avatar_label')"
      >
        <o-file-list
          :files="(current as User).avatar"
          class="mb-2"
          confirmable
          @delete="fetchItems"
        />
      </v-file>
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
import { rolesApi, usersApi } from '../api'
import OModal from '@/components/OModal.vue'
import { useItems } from '@/composables/useItems'
import { usePage } from '@/composables/usePage'
import { useAuthStore } from '@/stores/auth'
import { Item, ItemAction } from '@/types'
import { Role, User } from '../types'
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { sanitizeUrl } from '@braintree/sanitize-url'
import { IconSquare, IconSquareCheck } from '@tabler/icons-vue'

const { t } = useI18n()

const authStore = useAuthStore()

// page
usePage({
  title: t('access.users.title'),
})

// modal
const modal = ref<typeof OModal | null>(null)

// defaults
const defaults = {
  name: '',
  email: '',
  avatar: null,
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
  fetchItems,
  paginate,
  sort,
  add,
  edit,
  submit,
  destroy,
  cleanRoute,
} = useItems<User>({
  api: usersApi,
  defaults,
  modal,
  listKey: 'users',
})

// disable
const disable = async (item: number | User) => {
  const id = typeof item === 'number' ? item : item.id
  await usersApi.disable(id)
  fetchItems()
}

// enable
const enable = async (item: number | User) => {
  const id = typeof item === 'number' ? item : item.id
  await usersApi.enable(id)
  fetchItems()
}

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
const roles = ref<Array<Role>>([])
onMounted(async () => {
  const { data } = await rolesApi.all()
  roles.value = data as Array<Role>
})

// submit user
const submitUser = async () => {
  await submit()
  if (current.value.id === authStore.user?.id) {
    authStore.fetchUser()
  }
}
</script>
