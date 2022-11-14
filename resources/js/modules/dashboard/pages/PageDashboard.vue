<template>
  <app-verify-alert />
  <div>coming soon...</div>
</template>

<script setup lang="ts">
import AppVerifyAlert from '@/components/app/AppVerifyAlert.vue'
import { usePage } from '@/composables/usePage'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// page
usePage({
  title: t('dashboard.title'),
})
</script>
