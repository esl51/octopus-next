<template>
  <b-dropdown
    variant="link"
    no-caret
    toggle-class="btn-action btn-action-table"
    strategy="fixed"
  >
    <template #button-content>
      <o-icon name="dots-vertical" />
    </template>
    <b-dropdown-item-button
      v-for="(action, index) in allActions"
      :key="'action-' + index"
      :disabled="action.disabled"
      :variant="!action.disabled ? (action.variant as ColorVariant) : undefined"
      @click="action.handler"
    >
      <o-icon
        v-if="action.icon"
        :name="action.icon"
        class="dropdown-item-icon"
        :class="{
          [`text-${action.variant}`]: !!action.variant && !action.disabled,
          'text-muted': action.disabled,
        }"
      />
      {{ action.label }}
    </b-dropdown-item-button>
  </b-dropdown>
</template>

<script setup lang="ts">
import { Item, ItemAction } from '@/types'
import { ColorVariant } from 'bootstrap-vue-next'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// props
const props = withDefaults(
  defineProps<{
    item: Item
    actions?: Array<ItemAction>
    edit?: boolean
    delete?: boolean
  }>(),
  {
    actions: undefined,
    edit: true,
    delete: true,
  },
)

// emits
const emit = defineEmits(['edit', 'delete'])

const defaultActions = computed<Array<ItemAction>>(() => {
  const actions: Array<ItemAction> = []
  if (props.edit) {
    actions.push({
      label: t('global.edit'),
      icon: 'edit',
      disabled: !props.item.is_editable,
      handler: () => {
        emit('edit', props.item)
      },
    })
  }
  if (props.delete) {
    actions.push({
      label: t('global.delete'),
      icon: 'trash',
      variant: 'danger',
      disabled: !props.item.is_deletable,
      handler: () => {
        emit('delete', props.item)
      },
    })
  }
  return actions
})

const allActions = computed<Array<ItemAction>>(() => ({
  ...defaultActions.value,
  ...props.actions,
}))
</script>
