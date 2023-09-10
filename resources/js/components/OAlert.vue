<template>
  <div
    ref="alert"
    :class="{
      alert: true,
      ['alert-' + variant]: !!variant,
      'alert-dismissible': dismissible,
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
import Alert from 'bootstrap/js/dist/alert'
import { ref } from 'vue'

// props
withDefaults(
  defineProps<{
    variant?: string
    dismissible?: boolean
  }>(),
  {
    variant: 'info',
    dismissible: false,
  },
)

const alert = ref(null)
if (alert.value) {
  new Alert(alert.value)
}
</script>
