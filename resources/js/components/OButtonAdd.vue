<template>
  <o-button
    v-b-tooltip="$t('global.add')"
    variant="primary"
    icon
    @click="emit('click')"
  >
    <plus-icon class="icon" />
  </o-button>
</template>

<script setup lang="ts">
const emit = defineEmits(['click'])
</script>
