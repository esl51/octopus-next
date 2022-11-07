<template>
  <component :is="layout">
    <router-view />
  </component>
</template>

<script setup lang="ts">
import { markRaw, ref, watch } from 'vue'
import { useRoute } from 'vue-router'

const layout = ref()
const route = useRoute()

watch(
  () => route.meta?.layout,
  async (metaLayout?: string) => {
    const layoutName = metaLayout
      ? metaLayout[0].toUpperCase() + metaLayout.slice(1)
      : 'Default'
    const component = await import(
      /* @vite-ignore */ `./AppLayout${layoutName}.vue`
    )
    layout.value = markRaw(component.default)
  },
  {
    immediate: true,
  }
)
</script>
