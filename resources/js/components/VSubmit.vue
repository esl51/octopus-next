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
import { ButtonType, ButtonVariant, Size } from 'bootstrap-vue-3'
import Form from 'vform'
import { inject } from 'vue'

// props
withDefaults(
  defineProps<{
    disabled?: boolean
    type?: ButtonType
    variant?: ButtonVariant
    size?: Size
  }>(),
  {
    disabled: false,
    type: 'submit',
    variant: 'primary',
    size: undefined,
  }
)

// form
const form: Form | undefined = inject('form')
</script>
