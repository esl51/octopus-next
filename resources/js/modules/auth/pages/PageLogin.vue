<template>
  <b-card class="card-md">
    <h1 class="card-title text-center mb-4">{{ $t('auth.login.headline') }}</h1>
    <v-form
      :form="form"
      @submit="login"
    >
      <v-input
        :label="$t('auth.login.email_label')"
        :placeholder="$t('auth.login.email_placeholder')"
        name="email"
        type="email"
        icon="user"
        autofocus
      />
      <v-input
        :label="$t('auth.login.password_label')"
        :placeholder="$t('auth.login.password_placeholder')"
        name="password"
        type="password"
        icon="key"
      >
        <template
          v-if="$appConfig.authFeatures.includes('reset-passwords')"
          #labelDescription
        >
          <router-link :to="{ name: 'auth.forgotPassword' }">
            {{ $t('auth.login.forgot_password_link') }}
          </router-link>
        </template>
      </v-input>
      <v-checkbox
        :label="$t('auth.login.remember_label')"
        name="remember"
        class="mb-3"
      />
      <template #footer>
        <v-submit class="w-100">
          {{ $t('auth.login.submit_button') }}
        </v-submit>
      </template>
    </v-form>
  </b-card>
  <div
    v-if="$appConfig.authFeatures.includes('registration')"
    class="text-center text-muted mt-3"
  >
    {{ $t('auth.login.register_title') }}
    <router-link :to="{ name: 'auth.register' }">
      {{ $t('auth.login.register_link') }}
    </router-link>
  </div>
</template>

<script setup lang="ts">
import { authApi, authUrls } from '@/api/auth'
import { usePage } from '@/composables/usePage'
import { useAuthStore } from '@/stores/auth'
import Form from 'vform'
import { reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

// page
usePage({
  title: t('auth.login.title'),
})

// form
const form = reactive(
  new Form({
    email: '',
    password: '',
    remember: false,
  }),
)

// login
const login = async () => {
  await authApi.csrfCookie()
  await form.post(authUrls.login)
  await authStore.fetchUser()
  if (route.query?.intended_url) {
    router.push(route.query.intended_url as string)
  } else if (route.query?.redirect_url) {
    window.location.href = route.query?.redirect_url as string
  } else {
    router.push({ name: 'home' })
  }
}
</script>
