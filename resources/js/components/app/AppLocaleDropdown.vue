<template>
  <div class="dropdown">
    <o-button
      :variant="variant"
      :size="size"
      class="dropdown-toggle"
      data-bs-toggle="dropdown"
    >
      {{ locales[locale] }}
    </o-button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li>
        <button
          v-for="newLocale in langLocales"
          :key="newLocale"
          class="dropdown-item"
          @click.prevent="setLocale(newLocale)"
        >
          {{ locales[newLocale] }}
        </button>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { useLangStore } from '@/stores/lang'
import { LangLocale } from '@/types'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'

const langStore = useLangStore()
const i18n = useI18n()
const router = useRouter()

// props
withDefaults(
  defineProps<{
    variant?: string
    size?: 'sm' | 'lg'
    right?: boolean
  }>(),
  {
    variant: 'default',
    size: undefined,
    right: true,
  },
)

// locale
const locale = computed(() => langStore.locale)
const locales = computed(() => langStore.locales)
const langLocales = computed(
  () => Object.keys(langStore.locales) as Array<LangLocale>,
)
const setLocale = (newLocale: LangLocale) => {
  if (i18n.locale.value !== newLocale) {
    langStore.setLocale(newLocale)
    router.go(0)
  }
}
</script>
