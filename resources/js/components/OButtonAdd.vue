<template>
  <o-button
    v-tooltip
    :title="$t('global.add')"
    variant="primary"
    icon
    @click="emit('click')"
  >
    <o-icon name="plus" />
  </o-button>
</template>

<script setup lang="ts">
const emit = defineEmits(['click'])
</script>
