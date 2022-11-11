<template>
  <div class="page">
    <app-sidebar />
    <app-header />
    <div class="page-wrapper">
      <div class="page-header d-print-none">
        <div class="container-xl">
          <div class="row g-2 align-items-center">
            <div class="col">
              <app-breadcrumb class="mb-1" />
              <h1 class="page-title">{{ title }}</h1>
            </div>
            <div class="col-12 col-sm-auto ms-auto d-print-none">
              <div
                id="page-buttons"
                class="btn-list"
              />
            </div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="container-xl">
          <app-verify-alert />
          <slot />
        </div>
      </div>
      <app-footer />
    </div>
  </div>
</template>

<script setup lang="ts">
import AppBreadcrumb from '@/components/app/AppBreadcrumb.vue'
import AppFooter from '@/components/app/AppFooter.vue'
import AppHeader from '@/components/app/AppHeader.vue'
import AppSidebar from '@/components/app/AppSidebar.vue'
import AppVerifyAlert from '@/components/app/AppVerifyAlert.vue'
import { usePageStore } from '@/stores/page'
import { computed } from 'vue'

const pageStore = usePageStore()
const title = computed(() => pageStore.title)
</script>
