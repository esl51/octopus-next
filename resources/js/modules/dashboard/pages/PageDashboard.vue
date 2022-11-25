<template>
  <o-page-header :title="$t('dashboard.title')" />
  <app-verify-alert />
  <v-form :form="form">
    <v-editor
      name="ta"
      label="ta"
      translatable
    />
    {{ form['ta:ru'] }}
    {{ form['ta:en'] }}
    <hr />
    <v-select
      name="roles"
      label="roles"
      :placeholder="$t('global.choose')"
      :options="roles"
      label-attribute="title"
      size="sm"
      multiple
    />
    {{ form.roles }}
    <hr />
    <v-select
      name="role"
      label="role"
      :placeholder="$t('global.choose')"
      :options="roles"
      label-attribute="title"
    />
    {{ form.role }}
    <hr />
    <v-select
      name="roles_async"
      label="roles (async)"
      :options="rolesAsync"
      :initial-options="user && user.roles"
      :placeholder="$t('global.choose')"
      label-attribute="title"
      multiple
      size="lg"
      @search="searchRoles"
    />
    {{ form.roles_async }}
  </v-form>
</template>

<script setup lang="ts">
import AppVerifyAlert from '@/components/app/AppVerifyAlert.vue'
import { usePage } from '@/composables/usePage'
import { debounce } from '@/helpers'
import { rolesApi } from '@/modules/access/api'
import { useAuthStore } from '@/stores/auth'
import { Item } from '@/types'
import Form from 'vform'
import { computed, onMounted, reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const form = reactive(
  new Form({
    roles: [2, 1],
    role: 1,
    roles_async: [],
    'ta:en': '',
    'ta:ru': '<p>test</p>',
  })
)

const { t } = useI18n()

// page
usePage({
  title: t('dashboard.title'),
})

// user
const authStore = useAuthStore()
const user = computed(() => authStore.user)
form.roles_async = user.value?.roles?.map((r) => r.id)

const rolesAsync = ref<Array<Item>>([])
const searchRoles = debounce(
  async (search: string, busy: (b: boolean) => void) => {
    busy(true)
    try {
      const { data } = await rolesApi.list({ search })
      rolesAsync.value = data
    } finally {
      busy(false)
    }
  },
  300
)

// roles
const roles = ref<Array<Item>>([])
onMounted(async () => {
  const { data } = await rolesApi.all()
  roles.value = data
})
</script>
