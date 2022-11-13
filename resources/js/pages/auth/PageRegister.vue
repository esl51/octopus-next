<template>
  <b-card class="card-md">
    <h1 class="card-title text-center mb-4">
      {{ $t('auth.register.headline') }}
    </h1>
    <v-form
      :form="form"
      @submit="register"
    >
      <v-input
        :label="$t('auth.register.name_label')"
        :placeholder="$t('auth.register.name_placeholder')"
        name="name"
        autofocus
      />
      <v-input
        :label="$t('auth.register.email_label')"
        :placeholder="$t('auth.register.email_placeholder')"
        name="email"
        type="email"
        autocomplete="new-password"
      />
      <v-input
        :label="$t('auth.register.password_label')"
        :placeholder="$t('auth.register.password_placeholder')"
        name="password"
        type="password"
        autocomplete="new-password"
      />
      <template #footer>
        <v-submit class="w-100">
          {{ $t('auth.register.submit_button') }}
        </v-submit>
      </template>
    </v-form>
  </b-card>
  <div class="text-center text-muted mt-3">
    {{ $t('auth.register.login_title') }}
    <router-link :to="{ name: 'auth.login' }">
      {{ $t('auth.register.login_link') }}
    </router-link>
  </div>
</template>

<script setup lang="ts">
import { authUrls } from '@/api/auth'
import { usePage } from '@/composables/usePage'
import { useAuthStore } from '@/stores/auth'
import Form from 'vform'
import { reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const { t } = useI18n()

// page
usePage({
  title: t('auth.register.title'),
})

// form
const form = reactive(
  new Form({
    name: '',
    email: '',
    password: '',
  })
)

// register
const register = async () => {
  await form.post(authUrls.register)
  await authStore.fetchUser()
  router.push({ name: 'home' })
}
</script>
