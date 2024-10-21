<template>
  <o-page-header :title="$t('dashboard.title')" />
  <app-verify-alert />
  <o-card>
    <h3 class="card-title">...</h3>
  </o-card>
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
