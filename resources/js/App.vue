<template>
  <transition
    name="fade"
    mode="out-in"
  >
    <app-layout />
  </transition>
  <div
    class="toast-container top-0 start-50 position-fixed p-3 translate-middle-x"
  >
    <mount-target name="toasts" />
  </div>
  <mount-target name="confirms" />
</template>

<script setup lang="ts">
import { useToast } from './composables/useToast'
import AppLayout from './layouts/AppLayout.vue'
import { useAuthStore } from './stores/auth'
import { usePageStore } from './stores/page'
import { useLangStore } from '@/stores/lang'
import { useHead } from '@vueuse/head'
import axios from 'axios'
import { computed } from 'vue'
import { MountTarget } from 'vue3-mount'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'

const authStore = useAuthStore()
const langStore = useLangStore()
const pageStore = usePageStore()
const router = useRouter()
const route = useRoute()
const { t } = useI18n()
const { danger, warning } = useToast()

// page
const appName = window.config.name
const title = computed(() =>
  pageStore.title ? pageStore.title + ' • ' + appName : appName
)
useHead({ title })

// axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// axios request interceptor
axios.interceptors.request.use((config) => {
  const locale = langStore.locale
  if (locale) {
    config.headers = config.headers ?? {}
    config.headers['Accept-Language'] = locale
  }

  return config
})

// axios response interceptor
axios.interceptors.response.use(
  (response) => response,
  async (error) => {
    const { status, data } = error.response

    // server error
    if (status >= 500) {
      danger({
        title: t('error.alert_title'),
        body: t('error.alert_text'),
      })
    }

    // bad request
    else if (status === 400 && data.status) {
      danger({
        title: t('error.alert_title'),
        body: data.status,
      })
    }

    // not found
    else if (status === 404 && data.message) {
      danger({
        title: t('error.alert_title'),
        body: data.message,
      })
    }

    // validation
    else if (status === 422 && data.message) {
      danger({
        body: data.message,
      })
    }

    // csrf token mismatch
    else if (status === 419 && authStore.check) {
      warning({
        title: t('error.csrf_mismatch_title'),
        body: t('error.csrf_mismatch_text'),
      })
      router.go(0)
    }

    // session expired
    else if (status === 401 && authStore.check) {
      warning({
        title: t('error.session_expired_title'),
        body: t('error.session_expired_text'),
      })
      await authStore.logout()
      router.push({ name: 'auth.login', query: { intended_url: route.path } })
    }

    return Promise.reject(error)
  }
)
</script>
