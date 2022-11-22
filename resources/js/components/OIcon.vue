<template>
  <component
    :is="icon"
    viewBox="0 0 24 24"
  />
</template>

<script setup lang="ts">
import { defineAsyncComponent, markRaw, ref, watch } from 'vue'

const props = defineProps<{
  name: string
}>()

const icon = ref()

watch(
  () => props.name,
  (name) => {
    icon.value = markRaw(
      defineAsyncComponent(
        () => import(`../../../node_modules/@tabler/icons/icons/${name}.svg`)
      )
    )
  },
  { immediate: true }
)
</script>
