<template>
  <aside class="navbar navbar-expand-lg navbar-vertical">
    <div class="container-fluid">
      <button
        :key="$route.path"
        class="navbar-toggler"
        data-bs-toggle="collapse"
        data-bs-target="#sidebar-menu"
      >
        <span class="navbar-toggler-icon" />
      </button>
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
      <ul class="navbar-nav flex-row d-lg-none">
        <app-user-menu />
      </ul>
      <div
        id="sidebar-menu"
        class="collapse navbar-collapse"
      >
        <ul class="navbar-nav pt-lg-3">
          <template v-for="(item, index) in nav">
            <li
              v-if="!item.children && checkPermissions(item) && item.to"
              :key="'nav-' + index"
              class="nav-item"
              :class="{
                dropdown: !!item.children,
                active: navItemActive(item),
              }"
            >
              <router-link
                class="nav-link"
                :to="item.to"
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
              </router-link>
            </li>
            <li
              v-else-if="checkPermissions(item)"
              :key="'nav-dd-' + index"
              class="nav-item dropdown"
              :class="{
                active: navItemActive(item),
              }"
            >
              <a
                class="nav-link dropdown-toggle"
                href="#"
                data-bs-toggle="dropdown"
                :class="{
                  show: navItemActive(item),
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
              </a>
              <div
                class="dropdown-menu"
                :class="{
                  show: navItemActive(item),
                  'd-block': navItemActive(item),
                }"
              >
                <template
                  v-for="(child, childIndex) in item.children"
                  :key="'nav-child-' + childIndex"
                >
                  <router-link
                    v-if="
                      (!child.permissions ||
                        authStore?.canAny(child.permissions)) &&
                      child.to
                    "
                    class="dropdown-item"
                    active-class="active"
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
                  </router-link>
                </template>
              </div>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </aside>
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
    // without permissions & has accessible children
    (!item.permissions &&
      item.children?.some(
        (c) =>
          !c.permissions || (c.permissions && authStore.canAny(c.permissions)),
      )) ||
    // without permissions & without children
    (!item.permissions && !item.children?.length) ||
    // has permissions
    (item.permissions && authStore.canAny(item.permissions))
  )
}
</script>
