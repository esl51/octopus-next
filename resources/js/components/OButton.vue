<template>
  <b-button
    :type="type as ButtonType"
    :disabled="disabled"
    :variant="variant as ButtonVariant"
    :size="size as Size"
    :class="classes"
  >
    <b-spinner
      v-if="busy"
      class="me-2"
      small
    />
    <slot />
  </b-button>
</template>

<script setup lang="ts">
import { ButtonType, ButtonVariant, Size } from 'bootstrap-vue-next'
import { computed } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    disabled?: boolean
    busy?: boolean
    type?: string
    variant?: string
    size?: string
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
  'btn-icon': !!props.icon,
}))
</script>
