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
            <!--
            <div class="col-12 col-md-auto ms-auto d-print-none">
              <div class="btn-list">
                <span class="d-none d-sm-inline">
                  <a
                    href="#"
                    class="btn"
                  >
                    New view
                  </a>
                </span>
                <a
                  href="#"
                  class="btn btn-primary d-none d-sm-inline-block"
                  data-bs-toggle="modal"
                  data-bs-target="#modal-report"
                >
                  <plus-icon />
                  Create new report
                </a>
                <a
                  href="#"
                  class="btn btn-primary d-sm-none btn-icon"
                  data-bs-toggle="modal"
                  data-bs-target="#modal-report"
                  aria-label="Create new report"
                >
                  <plus-icon />
                </a>
              </div>
            </div>
            -->
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
