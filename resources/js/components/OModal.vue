<template>
  <div
    class="modal modal-blur fade"
    aria-modal="true"
    role="dialog"
    :class="classes"
    :style="styles"
    @mousedown.self="backdropClick"
    @touchstart.self="backdropClick"
  >
    <div
      ref="dialog"
      class="modal-dialog modal-dialog-centered"
      role="document"
      :class="dialogClasses"
    >
      <div class="modal-content">
        <div
          v-if="closeButton"
          class="modal-header"
        >
          <h5
            v-if="title"
            class="modal-title"
          >
            {{ title }}
          </h5>
          <button
            type="button"
            class="btn-close"
            :aria-label="$t('global.close')"
            @click="hide"
          />
        </div>
        <div class="modal-body">
          <div
            v-if="!closeButton && title"
            class="modal-title"
          >
            {{ title }}
          </div>
          <slot />
        </div>
        <div
          v-if="$slots.footer"
          class="modal-footer"
        >
          <slot name="footer" />
        </div>
      </div>
    </div>
  </div>
  <div
    v-if="!localHidden"
    class="modal-backdrop fade"
    :class="backdropClasses"
  />
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    title?: string
    size?: string
    closeButton?: boolean
    hideOnBackdropClick?: boolean
  }>(),
  {
    title: undefined,
    size: undefined,
    closeButton: true,
    hideOnBackdropClick: true,
  }
)

// emits
const emit = defineEmits(['backdropClick', 'show', 'hide'])

const localShow = ref(false)
const localHidden = ref(true)

// modal classes and styles
const classes = computed(() => ({
  show: localShow.value,
}))
const dialogClasses = computed(() => ({
  [`modal-${props.size}`]: !!props.size,
}))
const backdropClasses = computed(() => ({
  show: localShow.value,
}))
const styles = computed(() =>
  localHidden.value ? 'display: none;' : 'display: block;'
)
const dialog = ref<HTMLElement | null>(null)
const autofocusInput = computed(
  () => dialog.value?.querySelector('[autofocus],.autofocus') as HTMLElement
)

// show
const show = () => {
  emit('show')
  localHidden.value = false
  setTimeout(() => {
    localShow.value = true
    autofocusInput.value?.focus()
    const body = document.querySelector('body')
    if (body) {
      body.classList.add('modal-open')
      body.style.paddingRight = '8px'
      body.style.overflow = 'hidden'
    }
  }, 1)
}

// hide
const hide = () => {
  emit('hide')
  localShow.value = false
  setTimeout(() => {
    localHidden.value = true
    const shownModals = document.querySelectorAll('.modal.show')
    const body = document.querySelector('body')
    if (body && !shownModals.length) {
      body.classList.remove('modal-open')
      body.style.paddingRight = ''
      body.style.overflow = ''
    }
  }, 300)
}

// backdrop click
const backdropClick = () => {
  emit('backdropClick')
  if (localShow.value && props.hideOnBackdropClick) {
    hide()
  }
}

// expose
defineExpose({ show, hide })
</script>
