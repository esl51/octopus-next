<template>
  <b-navbar
    toggleable="lg"
    tag="aside"
    class="navbar-vertical"
    dark
  >
    <b-navbar-toggle v-b-toggle.sidebar-menu>
      <span class="navbar-toggler-icon" />
    </b-navbar-toggle>
    <router-link
      :to="{ name: 'home' }"
      class="navbar-brand navbar-brand-autodark"
    >
      <img
        src="/img/logo-white.svg"
        height="26"
        :alt="$appConfig.name"
      />
    </router-link>
    <b-navbar-nav class="flex-row d-lg-none">
      <app-user-menu />
    </b-navbar-nav>
    <b-collapse
      id="sidebar-menu"
      class="navbar-collapse"
    >
      <b-navbar-nav class="pt-lg-3">
        <b-nav-item :to="{ name: 'dashboard' }">
          <span class="nav-link-icon d-md-none d-lg-inline-block">
            <dashboard-icon />
          </span>
          <span class="nav-link-title">
            {{ $t('dashboard.title') }}
          </span>
        </b-nav-item>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script setup lang="ts">
import AppUserMenu from './AppUserMenu.vue'
</script>
