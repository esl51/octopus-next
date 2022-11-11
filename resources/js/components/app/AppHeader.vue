<template>
  <b-navbar
    toggleable="md"
    tag="header"
    class="navbar-light d-none d-lg-flex d-print-none"
    container="xl"
  >
    <b-navbar-nav class="flex-row order-md-last ms-auto">
      <div class="nav-item d-none d-md-flex me-3">
        <div class="btn-list">
          <app-locale-dropdown variant="link" />
        </div>
      </div>
      <app-user-menu />
    </b-navbar-nav>
    <div id="page-search" />
  </b-navbar>
</template>

<script setup lang="ts">
import AppUserMenu from './AppUserMenu.vue'
import AppLocaleDropdown from '@/components/app/AppLocaleDropdown.vue'
</script>
