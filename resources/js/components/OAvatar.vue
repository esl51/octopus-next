<template>
  <span
    class="avatar"
    :class="classes"
    :style="style"
  >
    <o-icon
      v-if="!image && icon"
      :name="icon"
      class="avatar-icon"
    />
    <template v-else-if="!image && !icon && placeholder">
      {{ placeholder }}
    </template>
    <slot />
  </span>
</template>

<script setup lang="ts">
import { computed, StyleValue } from 'vue'

// props
const props = defineProps<{
  image?: string | null
  placeholder?: string
  icon?: string
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
  props.image ? `background-image: url("${props.image}");` : undefined
)
</script>
