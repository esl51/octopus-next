<template>
  <img
    :src="'/img/logo' + (white ? '-white' : '') + '.svg'"
    height="26"
    :alt="$appConfig.name"
  />
</template>

<script setup lang="ts">
// props
withDefaults(
  defineProps<{
    white?: boolean
  }>(),
  {
    white: false,
  },
)
</script>
