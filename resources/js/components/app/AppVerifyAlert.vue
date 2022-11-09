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
    <div class="d-flex">
      <div>
        <alert-triangle-icon class="icon alert-icon" />
      </div>
      <div>
        <h3 class="mb-1">{{ $t('auth.verification.alert_title') }}</h3>
        <p>{{ $t('auth.verification.alert_body') }}</p>
        <div class="btn-list">
          <o-button
            variant="warning"
            :busy="busy"
            @click="send"
          >
            {{ $t('auth.verification.alert_send_button') }}
          </o-button>
        </div>
      </div>
    </div>
  </b-alert>
  <b-alert
    v-else-if="
      $appConfig.authFeatures.includes('email-verification') &&
      verificationSent === true
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
        {{ $t('auth.verification.sent', { email: user?.email }) }}
      </div>
    </div>
  </b-alert>
  <b-alert
    v-else-if="
      $appConfig.authFeatures.includes('email-verification') &&
      verificationSent === false
    "
    variant="danger"
    show
    dismissible
  >
    <div class="d-flex">
      <div>
        <alert-circle-icon class="icon alert-icon" />
      </div>
      <div>
        {{ $t('auth.verification.too_many_attempts_error') }}
      </div>
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
import { ApiError } from '@/api'
import authApi from '@/api/auth'
import { useAuthStore } from '@/stores/auth'
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

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
  } catch (e) {
    const err = e as ApiError
    if (err.response?.status === 429) {
      verificationSent.value = false
    }
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
