<template>
  <div class="card">
    <div
      class="card-body"
      :class="bodyClass"
    >
      <slot />
    </div>
    <div
      v-if="$slots.footer"
      class="card-footer"
      :class="footerClass"
    >
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup lang="ts">
// props
withDefaults(
  defineProps<{
    bodyClass?: string
    footerClass?: string
  }>(),
  {
    bodyClass: '',
    footerClass: '',
  },
)
</script>
