<template>
  <button
    :type="type"
    :disabled="disabled"
    :class="classes"
  >
    <o-spinner
      v-if="busy"
      class="me-2"
      small
    />
    <slot />
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    disabled?: boolean
    busy?: boolean
    type?: 'button' | 'submit' | 'reset'
    variant?: string
    size?: 'sm' | 'lg'
    icon?: boolean
  }>(),
  {
    disabled: false,
    busy: false,
    type: 'button',
    variant: 'primary',
    size: undefined,
    icon: false,
  },
)

const classes = computed(() => ({
  btn: true,
  'btn-icon': !!props.icon,
  ['btn-' + props.variant]: !!props.variant,
  ['btn-' + props.size]: !!props.size,
}))
</script>
