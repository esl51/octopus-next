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
        <app-logo :white="theme === 'dark'" />
      </router-link>
      <ul class="navbar-nav flex-row d-lg-none">
        <app-user-menu />
      </ul>
      <o-collapse
        id="sidebar-menu"
        class="navbar-collapse"
      >
        <ul class="navbar-nav pt-lg-3">
          <template v-for="(item, index) in nav">
            <li
              v-if="!item.children && checkPermissions(item) && item.to"
              :key="'nav-' + index"
              class="nav-item"
              :class="{
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
                  <o-icon :type="item.icon" />
                </span>
                <span
                  v-if="item.label"
                  class="nav-link-title"
                >
                  {{ $t(item.label) }}
                </span>
              </router-link>
            </li>
            <o-dropdown
              v-else-if="checkPermissions(item)"
              :key="'nav-dd-' + index"
              tag="li"
              class="nav-item"
              :class="{
                active: navItemActive(item),
              }"
              toggle-anchor
              :toggle-class="{
                'nav-link': true,
                show: navItemActive(item),
              }"
              :menu-class="{
                show: navItemActive(item),
                'd-block': navItemActive(item),
              }"
            >
              <template #toggle>
                <span
                  v-if="item.icon"
                  class="nav-link-icon d-md-none d-lg-inline-block"
                >
                  <o-icon :type="item.icon" />
                </span>
                <span
                  v-if="item.label"
                  class="nav-link-title"
                >
                  {{ $t(item.label) }}
                </span>
              </template>
              <li
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
                    <o-icon :type="child.icon" />
                  </span>
                  <span
                    v-if="child.label"
                    class="nav-link-title"
                  >
                    {{ $t(child.label) }}
                  </span>
                </router-link>
              </li>
            </o-dropdown>
          </template>
        </ul>
      </o-collapse>
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
import AppLogo from './AppLogo.vue'

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
