<template>
  <o-card class="card-md">
    <h1 class="h2 text-center mb-4">{{ $t('auth.forgot_password.title') }}</h1>
    <p class="text-muted text-center mb-4">
      {{ $t('auth.forgot_password.text') }}
    </p>
    <v-form
      :form="form"
      @submit="submit"
    >
      <v-input
        :label="$t('auth.forgot_password.email_label')"
        :placeholder="$t('auth.forgot_password.email_placeholder')"
        name="email"
        type="email"
        icon="user"
        autofocus
      />
      <template #footer>
        <v-submit class="w-100">
          <o-icon name="mail" />
          {{ $t('auth.forgot_password.submit_button') }}
        </v-submit>
      </template>
    </v-form>
  </o-card>
  <div class="text-center text-muted mt-3">
    {{ $t('auth.forgot_password.login_title') }}
    <router-link :to="{ name: 'auth.login' }">
      {{ $t('auth.forgot_password.login_link') }}
    </router-link>
  </div>
</template>

<script setup lang="ts">
import { authUrls } from '@/api/auth'
import { usePage } from '@/composables/usePage'
import { useToast } from '@/composables/useToast'
import Form from 'vform'
import { reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'

const router = useRouter()
const { t } = useI18n()
const { success } = useToast()

// page
usePage({
  title: t('auth.forgot_password.title'),
})

// form
const form = reactive(
  new Form({
    email: '',
  }),
)

// submit
const submit = async () => {
  const { data } = await form.post(authUrls.forgotPassword)
  if (data.message) {
    success({
      body: data.message,
    })
  }
  router.push({ name: 'auth.login' })
}
</script>
