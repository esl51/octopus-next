<template>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li
        v-for="item in breadcrumb"
        :key="item.text"
        class="breadcrumb-item"
      >
        <router-link :to="item.to">
          {{ item.text }}
        </router-link>
      </li>
    </ol>
  </nav>
</template>

<script setup lang="ts">
import { usePageStore } from '@/stores/page'
import { computed } from 'vue'

const pageStore = usePageStore()
const breadcrumb = computed(() => pageStore.breadcrumb)
</script>
