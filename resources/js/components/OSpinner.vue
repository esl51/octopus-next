<template>
  <div
    :class="{
      'spinner-border': true,
      ['text-' + variant]: !!variant,
      'spinner-border-sm': small,
    }"
  />
</template>

<script setup lang="ts">
// props
withDefaults(
  defineProps<{
    variant?: string
    small?: boolean
  }>(),
  {
    variant: 'info',
    small: false,
  },
)
</script>
