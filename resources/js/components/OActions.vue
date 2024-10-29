<template>
  <o-dropdown
    no-caret
    toggle-class="btn-action btn-action-table"
    toggle-variant="link"
  >
    <template #toggle>
      <o-icon :type="IconDotsVertical" />
    </template>
    <li
      v-for="(action, index) in allActions"
      :key="'action-' + index"
    >
      <button
        :disabled="action.disabled"
        class="dropdown-item"
        :class="{
          ['text-' + action.variant]: !!action.variant && !action.disabled,
        }"
        @click="action.handler"
      >
        <o-icon
          v-if="action.icon"
          :type="action.icon"
          class="dropdown-item-icon"
          :class="{
            [`text-${action.variant}`]: !!action.variant && !action.disabled,
            'text-muted': action.disabled,
          }"
        />
        {{ action.label }}
      </button>
    </li>
  </o-dropdown>
</template>

<script setup lang="ts">
import { Item, ItemAction } from '@/types'
import { IconDotsVertical, IconEdit, IconTrash } from '@tabler/icons-vue'
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

const preActions = computed<Array<ItemAction>>(() => {
  const actions: Array<ItemAction> = []
  if (props.edit) {
    actions.push({
      label: t('global.edit'),
      icon: IconEdit,
      disabled: !props.item.is_editable,
      handler: () => {
        emit('edit', props.item)
      },
    })
  }
  return actions
})

const postActions = computed<Array<ItemAction>>(() => {
  const actions: Array<ItemAction> = []
  if (props.delete) {
    actions.push({
      label: t('global.delete'),
      icon: IconTrash,
      variant: 'danger',
      disabled: !props.item.is_deletable,
      handler: () => {
        emit('delete', props.item)
      },
    })
  }
  return actions
})

const allActions = computed<Array<ItemAction>>(() => [
  ...preActions.value,
  ...(props.actions?.filter((a) => !a.hidden) || []),
  ...postActions.value,
])
</script>
