<template>
  <a
    :title="$t(tooltip)"
    data-bs-toggle="tooltip"
    class="nav-link px-0"
    :aria-label="$t(tooltip)"
    href="#"
    @click.prevent="theme.toggleTheme"
  >
    <o-icon :name="icon" />
  </a>
</template>

<script setup lang="ts">
import { useThemeStore } from '@/stores/theme'
import { computed } from 'vue'

const theme = useThemeStore()

const icon = computed(() => (theme.theme === 'light' ? 'moon' : 'sun'))

const tooltip = computed(() =>
  theme.theme === 'light'
    ? 'global.enable_dark_mode'
    : 'global.enable_light_mode',
)
</script>
