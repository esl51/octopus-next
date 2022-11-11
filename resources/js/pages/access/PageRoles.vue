<template>
  <div>coming soon...</div>
</template>

<script setup lang="ts">
import { usePage } from '@/composables/usePage'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// page
usePage({
  title: t('access.roles.title'),
  breadcrumb: [
    {
      text: t('access.title'),
      to: { name: 'access.users' },
    },
  ],
})
</script>
