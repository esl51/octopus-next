<template>
  <b-card class="card-md">
    <h1 class="h2 text-center mb-4">{{ $t('auth.reset_password.title') }}</h1>
    <p class="text-muted text-center mb-4">
      {{ $t('auth.reset_password.text') }}
    </p>
    <v-form
      :form="form"
      @submit="submit"
    >
      <v-input
        v-model="form.email"
        :label="$t('auth.reset_password.email_label')"
        :placeholder="$t('auth.reset_password.email_placeholder')"
        name="email"
        type="email"
        autocomplete="new-password"
        autofocus
      />
      <v-input
        v-model="form.password"
        :label="$t('auth.reset_password.password_label')"
        :placeholder="$t('auth.reset_password.password_placeholder')"
        name="password"
        type="password"
        autocomplete="new-password"
      />
      <template #footer>
        <v-submit class="w-100">
          {{ $t('auth.reset_password.submit_button') }}
        </v-submit>
      </template>
    </v-form>
  </b-card>
</template>

<script setup lang="ts">
import { authUrls } from '@/api/auth'
import { usePage } from '@/composables/usePage'
import { useToast } from '@/composables/useToast'
import Form from 'vform'
import { reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()
const { success } = useToast()

// page
usePage({
  title: t('auth.reset_password.title'),
})

// form
const form = reactive(
  new Form({
    token: '',
    email: '',
    password: '',
  })
)

// submit
const submit = async () => {
  await router.isReady()
  form.token = route.query?.token
  const { data } = await form.post(authUrls.resetPassword)
  if (data.message) {
    success({
      body: data.message,
    })
  }
  router.push({ name: 'auth.login' })
}
</script>
