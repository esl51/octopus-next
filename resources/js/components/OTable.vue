<template>
  <div class="table-responsive">
    <table
      v-sortable="!!sortable ? {} : false"
      class="table table-vcenter datatable"
      :class="classes"
      @drag-end="move"
    >
      <thead>
        <tr>
          <th
            v-for="column in activeColumns"
            :key="column.key"
            :class="column.class"
          >
            <template v-if="column.sortable">
              <b-link @click.prevent="sort(column.key)">
                {{ column.title }}
              </b-link>
              <template v-if="params.sort_by === column.key">
                <o-icon
                  v-if="params.sort_desc !== undefined"
                  :name="params.sort_desc == 1 ? 'chevron-down' : 'chevron-up'"
                  class="icon-sm icon-thick"
                />
              </template>
            </template>
            <template v-else>
              {{ column.title }}
            </template>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="busy && !items.length">
          <td
            :colspan="activeColumns.length"
            class="text-center"
          >
            <b-spinner />
          </td>
        </tr>
        <tr v-else-if="!busy && !items.length">
          <td :colspan="activeColumns.length">
            <div class="empty">
              <div class="empty-icon">
                <o-icon name="mood-sad" />
              </div>
              <p class="empty-title">{{ $t('global.empty_title') }}</p>
              <p class="empty-subtitle text-muted">
                {{ $t('global.empty_body') }}
              </p>
            </div>
          </td>
        </tr>
        <tr
          v-for="item in items"
          v-else
          :key="item.id"
        >
          <td
            v-for="column in activeColumns"
            :key="column.key"
            :class="column.class"
            :data-title="column.title"
          >
            <slot
              :name="`cell(${column.key})`"
              :value="item[column.key]"
              :item="item"
            >
              {{ format(item[column.key], column.formatter) }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts" generic="T extends Item">
import { useFormatter } from '@/composables/useFormatter'
import { Item, ListParams } from '@/types'
import { OTableColumn } from '@/types'
import { computed } from 'vue'

const { formatDate, formatDateTime, formatTime, formatMoney, formatFileSize } =
  useFormatter()

// props
const props = withDefaults(
  defineProps<{
    data: Array<T>
    columns: Array<OTableColumn>
    params: ListParams
    stacked?: string
    sortable?: boolean
    busy?: boolean
    card?: boolean
  }>(),
  {
    stacked: 'md',
    sortable: false,
    busy: false,
    card: false,
  }
)

// emits
const emit = defineEmits(['sort', 'move'])

// items
const items = computed(() => props.data)

// active columns
const activeColumns = computed(() =>
  props.columns.filter((c) => c.disabled !== true)
)

// sort
const sort = (key: string) => {
  emit('sort', key)
}

// format
const format = (value: unknown, formatter?: OTableColumn['formatter']) => {
  if (typeof formatter === 'function') {
    return formatter(value)
  } else if (
    formatter === 'date' &&
    (value instanceof Date || typeof value === 'string')
  ) {
    return formatDate(value)
  } else if (
    formatter === 'datetime' &&
    (value instanceof Date || typeof value === 'string')
  ) {
    return formatDateTime(value)
  } else if (
    formatter === 'time' &&
    (value instanceof Date || typeof value === 'string')
  ) {
    return formatTime(value)
  } else if (
    formatter === 'money' &&
    (typeof value === 'string' || typeof value === 'number')
  ) {
    return formatMoney(value)
  } else if (formatter === 'filesize' && typeof value === 'number') {
    return formatFileSize(value)
  }
  return value
}

// move
const move = (event: Event) => {
  emit('move', event)
}

// classes
const classes = computed(() => ({
  [`table-stacked-${props.stacked}`]: !!props.stacked,
  draggable: !!props.sortable,
  'opacity-50': !!props.busy,
  'pe-none': !!props.busy,
  'card-table': !!props.card,
}))
</script>
