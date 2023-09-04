<template>
  <o-button
    :type="type"
    :disabled="form?.busy || disabled"
    :busy="form?.busy"
    :variant="variant"
    :size="size"
  >
    <slot />
  </o-button>
</template>

<script setup lang="ts">
import Form from 'vform'
import { inject } from 'vue'

// props
withDefaults(
  defineProps<{
    disabled?: boolean
    type?: 'button' | 'submit'
    variant?: string
    size?: 'sm' | 'lg'
  }>(),
  {
    disabled: false,
    type: 'submit',
    variant: 'primary',
    size: undefined,
  },
)

// form
const form: Form | undefined = inject('form')
</script>
