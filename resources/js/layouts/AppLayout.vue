<template>
  <component :is="layout">
    <router-view />
  </component>
</template>

<script setup lang="ts">
import { defineAsyncComponent, markRaw, ref, watch } from 'vue'
import { useRoute } from 'vue-router'

const layout = ref()
const route = useRoute()

watch(
  () => route.meta?.layout,
  async (metaLayout?: string) => {
    const layoutName = metaLayout
      ? metaLayout[0].toUpperCase() + metaLayout.slice(1)
      : 'Default'
    layout.value = markRaw(
      defineAsyncComponent(() => import(`./AppLayout${layoutName}.vue`)),
    )
  },
  {
    immediate: true,
  },
)
</script>
