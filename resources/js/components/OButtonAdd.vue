<template>
  <o-button
    :title="$t('global.add')"
    data-bs-toggle="tooltip"
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
