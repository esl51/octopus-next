<template>
  <div class="card">
    <div
      class="card-body"
      :class="bodyClass"
    >
      <h3
        v-if="title"
        class="card-title"
      >
        {{ title }}
      </h3>
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
    title?: string
    bodyClass?: string
    footerClass?: string
  }>(),
  {
    title: '',
    bodyClass: '',
    footerClass: '',
  },
)
</script>
