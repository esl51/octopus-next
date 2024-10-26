<template>
  <component
    :is="tag"
    ref="dropdown"
    class="dropdown"
  >
    <!-- eslint-disable sonarjs/no-vue-bypass-sanitization -->
    <component
      :is="toggleAnchor ? 'a' : 'o-button'"
      :variant="toggleAnchor ? null : toggleVariant"
      :size="toggleAnchor ? null : toggleSize"
      :href="toggleAnchor ? '#' : null"
      data-bs-toggle="dropdown"
      data-bs-popper-config='{ "strategy": "fixed" }'
      :class="toggleClasses"
    >
      <slot name="toggle" />
    </component>
    <!-- eslint-enable -->
    <component
      :is="menuTag"
      :class="menuClasses"
    >
      <slot />
    </component>
  </component>
</template>

<script setup lang="ts">
import Dropdown from 'bootstrap/js/dist/dropdown'
import { computed, ref } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    tag?: string
    noCaret?: boolean
    toggleAnchor?: boolean
    toggleClass?: string | object
    toggleVariant?: string
    toggleSize?: 'sm' | 'lg'
    menuTag?: string
    menuClass?: string | object
    menuEnd?: boolean
    menuArrow?: boolean
  }>(),
  {
    tag: 'div',
    noCaret: false,
    toggleAnchor: false,
    toggleClass: '',
    toggleVariant: '',
    toggleSize: undefined,
    menuTag: 'ul',
    menuClass: '',
    menuEnd: false,
    menuArrow: false,
  },
)

const dropdown = ref(null)
if (dropdown.value) {
  new Dropdown(dropdown.value)
}

// toggle classes
const toggleClasses = computed(() => {
  let classes: { [className: string]: boolean } = {
    'dropdown-toggle': !props.noCaret,
  }
  if (typeof props.toggleClass === 'string') {
    classes[props.toggleClass] = true
  } else if (typeof props.toggleClass === 'object') {
    classes = { ...classes, ...props.toggleClass }
  }
  return classes
})

// menu classes
const menuClasses = computed(() => {
  let classes: { [className: string]: boolean } = {
    'dropdown-menu': true,
    'dropdown-menu-end': props.menuEnd,
    'dropdown-menu-arrow': props.menuArrow,
  }
  if (typeof props.menuClass === 'string') {
    classes[props.menuClass] = true
  } else if (typeof props.menuClass === 'object') {
    classes = { ...classes, ...props.menuClass }
  }
  return classes
})
</script>
