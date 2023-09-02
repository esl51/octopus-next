<template>
  <b-dropdown
    :text="locales[locale]"
    :variant="variant as ButtonVariant"
    :size="size as Size"
    :right="right"
    class="shadow-none"
  >
    <b-dropdown-item
      v-for="newLocale in langLocales"
      :key="newLocale"
      @click.prevent="setLocale(newLocale)"
    >
      {{ locales[newLocale] }}
    </b-dropdown-item>
  </b-dropdown>
</template>

<script setup lang="ts">
import { useLangStore } from '@/stores/lang'
import { LangLocale } from '@/types'
import { ButtonVariant, Size } from 'bootstrap-vue-next/dist/src/types'
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
    size?: string
    right?: boolean
  }>(),
  {
    variant: 'default' as ButtonVariant,
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
