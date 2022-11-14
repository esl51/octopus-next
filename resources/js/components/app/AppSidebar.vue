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
        <template
          v-for="(item, index) in nav"
          :key="'nav-' + index"
        >
          <li
            v-if="checkPermissions(item)"
            class="nav-item"
            :class="{
              dropdown: !!item.children,
              active: navItemActive(item),
            }"
          >
            <b-link
              v-if="!item.children"
              :to="item.to"
              class="nav-link"
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
            <a
              v-else
              v-b-toggle="'sidebar-sub-' + index"
              class="nav-link dropdown-toggle"
              href="#"
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
            </a>
            <b-collapse
              v-if="!!item.children"
              :id="'sidebar-sub-' + index"
              :visible="navItemActive(item)"
            >
              <div
                v-if="item.children"
                class="dropdown-menu show"
              >
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
                      <component
                        :is="child.icon + '-icon'"
                        class="icon"
                      />
                    </span>
                    <span
                      v-if="child.label"
                      class="nav-link-title"
                    >
                      {{ $t(child.label) }}
                    </span>
                  </b-dropdown-item>
                </template>
              </div>
            </b-collapse>
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
    (item.to && router.resolve(item.to)?.name === route.name) ||
    (!!item.children &&
      route.matched.some(({ name }) =>
        item.children?.some((c) => c.to && router.resolve(c.to)?.name === name)
      ))
  )
}

const checkPermissions = (item: NavItem) => {
  return (
    !item.permissions ||
    authStore.canAny(item.permissions) ||
    (item.children &&
      item.children.some(
        (c) => !c.permissions || authStore.canAny(c.permissions)
      ))
  )
}
</script>
