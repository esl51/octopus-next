<template>
  <b-navbar
    toggleable="lg"
    tag="aside"
    class="navbar-vertical"
  >
    <b-navbar-toggle
      :key="$route.path"
      v-b-toggle.sidebar-menu
    >
      <span class="navbar-toggler-icon" />
    </b-navbar-toggle>
    <router-link
      :to="{ name: 'home' }"
      class="navbar-brand navbar-brand-autodark"
    >
      <img
        :src="'/img/logo' + (theme === 'dark' ? '-white' : '') + '.svg'"
        height="26"
        :alt="$appConfig.name"
      />
    </router-link>
    <b-navbar-nav class="flex-row d-lg-none">
      <app-user-menu />
    </b-navbar-nav>
    <b-collapse
      id="sidebar-menu"
      is-nav
    >
      <b-navbar-nav class="pt-lg-3">
        <template v-for="(item, index) in nav">
          <b-nav-item
            v-if="!item.children && checkPermissions(item)"
            :key="'nav-' + index"
            :to="item.to"
            :class="{
              dropdown: !!item.children,
              active: navItemActive(item),
            }"
          >
            <span
              v-if="item.icon"
              class="nav-link-icon d-md-none d-lg-inline-block"
            >
              <o-icon :name="item.icon" />
            </span>
            <span
              v-if="item.label"
              class="nav-link-title"
            >
              {{ $t(item.label) }}
            </span>
          </b-nav-item>
          <b-nav-item-dropdown
            v-else-if="checkPermissions(item)"
            :key="'nav-dd-' + index"
            :toggle-class="{
              show: navItemActive(item),
            }"
            :menu-class="{
              'w-auto': true,
              'd-block': navItemActive(item),
            }"
            :class="{
              active: navItemActive(item),
            }"
          >
            <template #button-content>
              <span
                v-if="item.icon"
                class="nav-link-icon d-md-none d-lg-inline-block"
              >
                <o-icon :name="item.icon" />
              </span>
              <span
                v-if="item.label"
                class="nav-link-title"
              >
                {{ $t(item.label) }}
              </span>
            </template>
            <template
              v-for="(child, childIndex) in item.children"
              :key="'nav-child-' + childIndex"
            >
              <b-dropdown-item
                v-if="
                  !child.permissions || authStore?.canAny(child.permissions)
                "
                :to="child.to"
              >
                <span
                  v-if="child.icon"
                  class="nav-link-icon d-md-none d-lg-inline-block"
                >
                  <o-icon :name="child.icon" />
                </span>
                <span
                  v-if="child.label"
                  class="nav-link-title"
                >
                  {{ $t(child.label) }}
                </span>
              </b-dropdown-item>
            </template>
          </b-nav-item-dropdown>
        </template>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script setup lang="ts">
import AppUserMenu from './AppUserMenu.vue'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import { NavItem } from '@/types'
import { computed } from 'vue'
import { inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const authStore = useAuthStore()

const themeStore = useThemeStore()
const theme = computed(() => themeStore.theme)

const nav: Array<NavItem> | undefined = inject('nav')

const navItemActive = (item: NavItem) =>
  (item.to && router.resolve(item.to)?.name === route.name) ||
  (!!item.children &&
    route.matched.some(
      ({ name }) =>
        item.children?.some((c) => c.to && router.resolve(c.to)?.name === name),
    ))

const checkPermissions = (item: NavItem) => {
  return (
    !item.permissions ||
    authStore.canAny(item.permissions) ||
    (item.children &&
      item.children.some(
        (c) => !c.permissions || authStore.canAny(c.permissions),
      ))
  )
}
</script>
