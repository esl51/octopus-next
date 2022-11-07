<template>
  <b-button
    :type="type"
    :disabled="disabled"
    :variant="variant"
    :size="size"
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
import { ButtonType, ButtonVariant, Size } from 'bootstrap-vue-3'

// props
withDefaults(
  defineProps<{
    disabled?: boolean
    busy?: boolean
    type?: ButtonType
    variant?: ButtonVariant
    size?: Size
  }>(),
  {
    disabled: false,
    busy: false,
    type: 'submit',
    variant: 'primary',
    size: undefined,
  }
)
</script>
