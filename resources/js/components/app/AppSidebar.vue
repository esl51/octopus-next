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
        <template
          v-for="(item, index) in nav"
          :key="'nav-' + index"
        >
          <li
            v-if="item.permissions && authStore.canAny(item.permissions)"
            class="nav-item"
            :class="{
              dropdown: !!item.children,
              active: navItemActive(item),
            }"
          >
            <b-link
              class="nav-link"
              :to="item.to"
              :data-bs-toggle="!!item.children ? 'dropdown' : null"
              :data-bs-auto-close="!!item.children ? false : null"
              :role="!!item.children ? 'button' : null"
              :aria-expanded="!!item.children ? navItemActive(item) : null"
              :class="{
                'dropdown-toggle': !!item.children,
              }"
            >
              <span
                v-if="item.icon"
                class="nav-link-icon d-md-none d-lg-inline-block"
              >
                <component
                  :is="item.icon + '-icon'"
                  class="icon"
                />
              </span>
              <span
                v-if="item.label"
                class="nav-link-title"
              >
                {{ $t(item.label) }}
              </span>
            </b-link>
            <div
              v-if="item.children"
              :id="'nav-dropdown-' + index"
              class="dropdown-menu"
              :class="{
                show: navItemActive(item),
              }"
            >
              <template
                v-for="(child, childIndex) in item.children"
                :key="'nav-child-' + childIndex"
              >
                <b-dropdown-item
                  v-if="
                    child.permissions && authStore?.canAny(child.permissions)
                  "
                  :to="child.to"
                >
                  <span
                    v-if="child.icon"
                    class="nav-link-icon d-md-none d-lg-inline-block"
                  >
                    <component
                      :is="child.icon + '-icon'"
                      class="icon"
                    />
                  </span>
                  {{ $t(child.label) }}
                </b-dropdown-item>
              </template>
            </div>
          </li>
        </template>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script setup lang="ts">
import AppUserMenu from './AppUserMenu.vue'
import { useAuthStore } from '@/stores/auth'
import { NavItem } from '@/types'
import { inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const authStore = useAuthStore()

const nav: Array<NavItem> | undefined = inject('nav')

const navItemActive = (item: NavItem) => {
  return (
    !!item.children &&
    route.matched.some(({ name }) =>
      item.children?.some((c) => c.to && router.resolve(c.to)?.name === name)
    )
  )
}
</script>
