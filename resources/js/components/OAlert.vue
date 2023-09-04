<template>
  <div
    :class="{
      alert: true,
      ['alert-' + variant]: !!variant,
      'alert-dismissible': dismissible,
      show,
    }"
  >
    <slot />
    <button
      v-if="dismissible"
      type="button"
      class="btn-close"
      data-bs-dismiss="alert"
      aria-label="Close"
    />
  </div>
</template>

<script setup lang="ts">
// props
withDefaults(
  defineProps<{
    variant?: string
    dismissible?: boolean
    show?: boolean
  }>(),
  {
    variant: 'info',
    dismissible: false,
    show: false,
  },
)
</script>
