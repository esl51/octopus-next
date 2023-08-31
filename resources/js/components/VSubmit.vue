<template>
  <o-button
    :type="type as ButtonType"
    :disabled="form?.busy || disabled"
    :busy="form?.busy"
    :variant="variant as ButtonVariant"
    :size="size as Size"
  >
    <slot />
  </o-button>
</template>

<script setup lang="ts">
import { ButtonType, ButtonVariant, Size } from 'bootstrap-vue-next'
import Form from 'vform'
import { inject } from 'vue'

// props
withDefaults(
  defineProps<{
    disabled?: boolean
    type?: string
    variant?: string
    size?: string
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
