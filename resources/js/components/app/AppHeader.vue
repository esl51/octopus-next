<template>
  <header
    class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none"
  >
    <div class="container-fluid">
      <ul class="navbar-nav flex-row order-md-last ms-auto">
        <li class="nav-item d-none d-md-flex me-3">
          <div class="btn-list">
            <app-locale-dropdown variant="link" />
            <app-theme-switch />
          </div>
        </li>
        <app-user-menu />
      </ul>
    </div>
  </header>
</template>

<script setup lang="ts">
import AppUserMenu from './AppUserMenu.vue'
import AppLocaleDropdown from '@/components/app/AppLocaleDropdown.vue'
import AppThemeSwitch from '@/components/app/AppThemeSwitch.vue'
</script>
