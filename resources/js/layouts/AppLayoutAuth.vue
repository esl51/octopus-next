<template>
  <div class="page page-center">
    <div class="container container-tight py-4">
      <div class="d-flex">
        <app-locale-dropdown
          class="ms-auto f-0"
          size="sm"
        />
      </div>
      <div class="text-center mb-4">
        <router-link
          :to="{ name: 'home' }"
          class="navbar-brand navbar-brand-autodark"
        >
          <app-logo />
        </router-link>
      </div>
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import AppLocaleDropdown from '@/components/app/AppLocaleDropdown.vue'
import AppLogo from '@/components/app/AppLogo.vue'
</script>
