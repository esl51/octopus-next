<template>
  <o-modal
    ref="modal"
    :title="title"
    :size="size"
    :close-button="false"
    :hide-on-backdrop-click="hideOnBackdropClick"
    @backdrop-click="backdropClick"
  >
    <div v-if="body">
      {{ body }}
    </div>
    <template #footer>
      <button
        type="button"
        class="btn"
        :class="noClasses"
        @click="no"
      >
        {{ noTitle || $t('global.cancel') }}
      </button>
      <button
        type="button"
        class="btn"
        :class="yesClasses"
        @click="yes"
      >
        {{ yesTitle || $t('global.ok') }}
      </button>
    </template>
  </o-modal>
</template>

<script setup lang="ts">
import OModal from './OModal.vue'
import { computed, onMounted, ref } from 'vue'

// props
const props = withDefaults(
  defineProps<{
    title?: string
    body?: string
    size?: string
    yesVariant?: string
    yesClass?: string
    yesTitle?: string
    hideOnYes?: boolean
    noVariant?: string
    noClass?: string
    noTitle?: string
    hideOnNo?: boolean
    hideOnBackdropClick?: boolean
  }>(),
  {
    title: undefined,
    body: undefined,
    size: 'sm',
    yesVariant: 'primary',
    yesClass: undefined,
    yesTitle: undefined,
    hideOnYes: true,
    noVariant: 'link',
    noClass: 'link-secondary me-auto',
    noTitle: undefined,
    hideOnNo: true,
    hideOnBackdropClick: true,
  }
)

// emits
const emit = defineEmits(['yes', 'no', 'backdropClick', 'show', 'hide'])

// modal
const modal = ref<typeof OModal | null>(null)

// yes classes
const yesClasses = computed(() => ({
  [`btn-${props.yesVariant}`]: props.yesVariant !== undefined,
  [`${props.yesClass}`]: props.yesClass !== undefined,
}))

// no classes
const noClasses = computed(() => ({
  [`btn-${props.noVariant}`]: props.noVariant !== undefined,
  [`${props.noClass}`]: props.noClass !== undefined,
}))

// show
const show = () => {
  emit('show')
  modal.value?.show()
}

// hide
const hide = () => {
  emit('hide')
  modal.value?.hide()
}

// backdrop click
const backdropClick = () => {
  emit('backdropClick')
}

// yes
const yes = () => {
  emit('yes')
  if (props.hideOnYes) {
    hide()
  }
}

// no
const no = () => {
  emit('no')
  if (props.hideOnNo) {
    hide()
  }
}

onMounted(() => {
  show()
})

// expose
defineExpose({ show, hide })
</script>
