<template>
  <div class="page">
    <app-sidebar />
    <app-header />
    <div class="page-wrapper">
      <div class="page-body">
        <div class="container-xl">
          <slot />
        </div>
      </div>
      <app-footer />
    </div>
  </div>
</template>

<script setup lang="ts">
import AppFooter from '@/components/app/AppFooter.vue'
import AppHeader from '@/components/app/AppHeader.vue'
import AppSidebar from '@/components/app/AppSidebar.vue'
import { usePageStore } from '@/stores/page'
import { computed } from 'vue'

const pageStore = usePageStore()
const title = computed(() => pageStore.title)
</script>
