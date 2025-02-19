<template>
  <div class="d-flex align-items-center min-w-0">
    <o-avatar
      :image="user.avatar?.url"
      :placeholder="user.name_placeholder"
      class="flex-shrink-0 me-2"
      :size="!email ? 'xs' : undefined"
    >
      <o-badge
        v-if="indicateDisabled && user?.disabled_at"
        variant="danger"
      />
      <o-badge
        v-else-if="
          indicateUnverified &&
          $appConfig.authFeatures.includes('email-verification') &&
          user?.email_verified_at === null
        "
        variant="warning"
      />
    </o-avatar>
    <div class="flex-fill min-w-0">
      <div class="fw-medium text-truncate">
        {{ user.name }}
      </div>
      <div
        v-if="email"
        class="text-muted fw-normal text-truncate"
      >
        <a
          :href="sanitizeUrl('mailto:' + user.email)"
          class="text-reset"
        >
          {{ user.email }}
        </a>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { User } from '@/modules/access/types'
import { sanitizeUrl } from '@braintree/sanitize-url'

// props
withDefaults(
  defineProps<{
    user: User
    indicateUnverified?: boolean
    indicateDisabled?: boolean
    email?: boolean
  }>(),
  {
    indicateUnverified: false,
    indicateDisabled: false,
    email: false,
  },
)
</script>
