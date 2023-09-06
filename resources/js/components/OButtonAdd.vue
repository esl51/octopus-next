<template>
  <o-button
    v-tooltip
    :title="$t('global.add')"
    variant="primary"
    icon
    @click="emit('click')"
  >
    <o-icon :type="IconPlus" />
  </o-button>
</template>

<script setup lang="ts">
import { IconPlus } from '@tabler/icons-vue'

const emit = defineEmits(['click'])
</script>
