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
            <dashboard-icon class="icon" />
          </span>
          <span class="nav-link-title">
            {{ $t('dashboard.title') }}
          </span>
        </b-nav-item>
        <li
          v-if="authStore?.canOne(['manage users', 'manage access'])"
          class="nav-item active dropdown"
        >
          <b-link
            class="nav-link dropdown-toggle"
            data-bs-toggle="dropdown"
            data-bs-auto-close="false"
            role="button"
            aria-expanded="true"
          >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <layout-2-icon class="icon" />
            </span>
            <span class="nav-link-title">
              {{ $t('access.title') }}
            </span>
          </b-link>
          <div
            id="nav-dropdown-1"
            class="dropdown-menu show"
          >
            <b-dropdown-item
              v-if="authStore?.canOne(['manage users', 'manage access'])"
              :to="{ name: 'access.users' }"
            >
              {{ $t('access.users.title') }}
            </b-dropdown-item>
            <b-dropdown-item
              v-if="authStore?.can('manage access')"
              :to="{ name: 'access.roles' }"
            >
              {{ $t('access.roles.title') }}
            </b-dropdown-item>
            <b-dropdown-item
              v-if="authStore?.can('manage access')"
              :to="{ name: 'access.permissions' }"
            >
              {{ $t('access.permissions.title') }}
            </b-dropdown-item>
          </div>
        </li>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script setup lang="ts">
import AppUserMenu from './AppUserMenu.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
</script>
