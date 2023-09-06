<template>
  <a
    v-tooltip
    :title="$t(tooltip)"
    class="nav-link px-0"
    :aria-label="$t(tooltip)"
    href="#"
    @click.prevent="theme.toggleTheme"
  >
    <o-icon :type="icon" />
  </a>
</template>

<script setup lang="ts">
import { useThemeStore } from '@/stores/theme'
import { IconSun, IconMoon } from '@tabler/icons-vue'
import { computed } from 'vue'

const theme = useThemeStore()

const icon = computed(() => (theme.theme === 'light' ? IconMoon : IconSun))

const tooltip = computed(() =>
  theme.theme === 'light'
    ? 'global.enable_dark_mode'
    : 'global.enable_light_mode',
)
</script>
