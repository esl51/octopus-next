<template>
  <div class="page page-center">
    <div class="container container-tight py-4">
      <div class="d-flex">
        <app-locale-dropdown
          class="ms-auto f-0"
          :variant="'default' as ButtonVariant"
          size="sm"
        />
      </div>
      <div class="text-center mb-4">
        <router-link
          :to="{ name: 'home' }"
          class="navbar-brand navbar-brand-autodark"
        >
          <img
            src="/img/logo.svg"
            height="36"
            :alt="$appConfig.name"
          />
        </router-link>
      </div>
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import AppLocaleDropdown from '@/components/app/AppLocaleDropdown.vue'
import { ButtonVariant } from 'bootstrap-vue-next'
</script>
