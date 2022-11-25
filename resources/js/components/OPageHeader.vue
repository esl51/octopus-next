<template>
  <div class="page-header d-print-none">
    <div class="row g-2 align-items-center">
      <div class="col">
        <component
          :is="titleTag"
          v-if="title"
          class="page-title"
        >
          {{ title }}
        </component>
        <div
          v-if="$slots.meta"
          class="text-muted mt-1"
        >
          <slot name="meta" />
        </div>
      </div>
      <div
        v-if="$slots.buttons"
        class="col-12 col-sm-auto ms-auto d-print-none"
      >
        <div class="btn-list">
          <slot name="buttons" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    title?: string
    titleTag?: string
  }>(),
  {
    title: undefined,
    titleTag: 'h1',
  }
)
</script>
