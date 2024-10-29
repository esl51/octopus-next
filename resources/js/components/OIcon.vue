<template>
  <component
    :is="type"
    viewBox="0 0 24 24"
    class="icon"
  />
</template>

<script setup lang="ts">
import { SVGProps } from '@/types'
import { FunctionalComponent } from 'vue'

defineProps<{
  type: FunctionalComponent<SVGProps>
}>()
</script>
