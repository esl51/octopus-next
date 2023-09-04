<template>
  <li class="nav-item dropdown">
    <a
      class="nav-link d-flex lh-1 text-reset p-0"
      data-bs-toggle="dropdown"
      :aria-label="$t('layout.open_user_menu')"
      aria-expanded="false"
      href="#"
    >
      <o-avatar
        :image="user?.avatar?.url"
        :placeholder="user?.name_placeholder"
        size="sm"
      >
        <span
          v-if="
            $appConfig.authFeatures.includes('email-verification') &&
            user?.email_verified_at === null
          "
          class="badge bg-warning"
        />
      </o-avatar>
      <div class="d-none d-xl-block ps-2">
        <div>{{ user?.name }}</div>
        <div
          v-if="user?.roles?.length"
          class="mt-1 small text-muted"
        >
          {{ user.roles[0].title }}
        </div>
      </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
      <li
        v-if="
          $appConfig.authFeatures.includes('update-profile-information') ||
          $appConfig.authFeatures.includes('update-passwords')
        "
      >
        <router-link
          :to="{ name: 'auth.profile' }"
          class="dropdown-item"
        >
          {{ $t('auth.profile.title') }}
          <span
            v-if="
              $appConfig.authFeatures.includes('email-verification') &&
              user?.email_verified_at === null
            "
            class="badge bg-warning ms-auto"
          />
        </router-link>
      </li>
      <button
        class="dropdown-item"
        @click="logout"
      >
        {{ $t('auth.logout.title') }}
      </button>
    </ul>
  </li>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { computed } from 'vue'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

// user
const user = computed(() => authStore.user)

// logout
const logout = async () => {
  await authStore.logout()
  router.push({ name: 'auth.login' })
}
</script>
