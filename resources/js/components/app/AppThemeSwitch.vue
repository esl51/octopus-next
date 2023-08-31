<template>
  <b-link
    v-b-tooltip="$t(tooltip)"
    class="nav-link px-0"
    :aria-label="$t(tooltip)"
    @click.prevent="theme.toggleTheme"
  >
    <o-icon :name="icon" />
  </b-link>
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
