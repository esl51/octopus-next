<template>
  <span :class="classes">
    <slot />
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    variant?: string
    light?: boolean
    pill?: boolean
  }>(),
  {
    variant: undefined,
    light: false,
    pill: false,
  },
)

// classes
const classes = computed(() => ({
  badge: true,
  'badge-pill': props.pill,
  ['bg-' + props.variant + (props.light ? '-lt' : '')]: !!props.variant,
  ['text-' + props.variant + '-fg']: !!props.variant && !props.light,
}))
</script>
