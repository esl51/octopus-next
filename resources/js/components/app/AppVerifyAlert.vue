<template>
  <b-alert
    v-if="
      $appConfig.authFeatures.includes('email-verification') &&
      verificationSent === null &&
      verified === false
    "
    :show="user?.email_verified_at === null"
    variant="warning"
  >
    <h3 class="mb-1">{{ $t('auth.verification.alert_title') }}</h3>
    <p>{{ $t('auth.verification.alert_body') }}</p>
    <div class="btn-list">
      <o-button
        variant="warning"
        :busy="busy"
        @click="send"
      >
        <mail-icon class="icon" />
        {{ $t('auth.verification.alert_send_button') }}
      </o-button>
    </div>
  </b-alert>
  <b-alert
    v-else-if="
      $appConfig.authFeatures.includes('email-verification') &&
      user?.email_verified_at !== null &&
      verified === true
    "
    variant="success"
    show
    dismissible
  >
    <div class="d-flex">
      <div>
        <check-icon class="icon alert-icon" />
      </div>
      <div>
        {{ $t('auth.verification.verified', { email: user?.email }) }}
      </div>
    </div>
  </b-alert>
</template>

<script setup lang="ts">
import authApi from '@/api/auth'
import { useToast } from '@/composables/useToast'
import { useAuthStore } from '@/stores/auth'
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const { t } = useI18n()
const { success } = useToast()

// user
const user = computed(() => authStore.user)

// verification
const verificationSent = ref<boolean | null>(null)
const verified = ref(false)

// send
const busy = ref(false)
const send = async () => {
  busy.value = true
  try {
    await authApi.verificationNotification()
    verificationSent.value = true
    success({
      body: t('auth.verification.sent', { email: user.value?.email }),
    })
  } finally {
    busy.value = false
  }
}

// mounted
onMounted(async () => {
  await router.isReady()
  if (route.query?.verified === '1') {
    await authStore.fetchUser()
    verified.value = user.value?.email_verified_at !== null
  }
})
</script>
