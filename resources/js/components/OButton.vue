<template>
  <b-button
    :type="type"
    :disabled="disabled"
    :variant="variant"
    :size="size"
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
import { ButtonType, ButtonVariant, Size } from 'bootstrap-vue-3'
import { computed } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    disabled?: boolean
    busy?: boolean
    type?: ButtonType
    variant?: ButtonVariant
    size?: Size
    icon?: boolean
  }>(),
  {
    disabled: false,
    busy: false,
    type: 'button',
    variant: 'primary',
    size: undefined,
    icon: false,
  }
)

const classes = computed(() => ({
  'btn-icon': !!props.icon,
}))
</script>
