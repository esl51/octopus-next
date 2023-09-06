<template>
  <span
    class="avatar"
    :class="classes"
    :style="style"
  >
    <o-icon
      v-if="!image && icon"
      :type="icon"
      class="avatar-icon"
    />
    <template v-else-if="!image && !icon && placeholder">
      {{ placeholder }}
    </template>
    <slot />
  </span>
</template>

<script setup lang="ts">
import { SVGProps } from '@tabler/icons-vue'
import { computed, FunctionalComponent, StyleValue } from 'vue'

// props
const props = defineProps<{
  image?: string | null
  placeholder?: string
  icon?: FunctionalComponent<SVGProps>
  size?: string
}>()

// classes
const classes = computed(() => {
  const classes: { [className: string]: boolean } = {}
  classes[`avatar-${props.size}`] = !!props.size
  return classes
})

//image
const style = computed<StyleValue | undefined>(() =>
  props.image ? `background-image: url("${props.image}");` : undefined,
)
</script>
