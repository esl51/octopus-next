<template>
  <b-link
    v-b-tooltip="$t(tooltip)"
    class="nav-link px-0"
    :aria-label="$t(tooltip)"
    @click="theme.toggleTheme"
  >
    <component
      :is="iconComponent"
      class="icon"
    />
  </b-link>
</template>

<script setup lang="ts">
import { useThemeStore } from '@/stores/theme'
import { computed } from 'vue'

const theme = useThemeStore()

const iconComponent = computed(
  () => (theme.theme === 'light' ? 'moon' : 'sun') + '-icon'
)

const tooltip = computed(() =>
  theme.theme === 'light'
    ? 'global.enable_dark_mode'
    : 'global.enable_light_mode'
)
</script>
