<template>
  <div class="table-responsive">
    <table
      class="table card-table table-vcenter text-nowrap datatable"
      :class="{
        [`table-stacked-${stacked}`]: !!stacked,
      }"
    >
      <thead>
        <tr>
          <th
            v-for="column in columns"
            :key="column.key"
            :class="column.class"
          >
            <template v-if="column.sortable">
              <b-link @click.prevent="sort(column.key)">
                {{ column.title }}
              </b-link>
              <template v-if="params.sort_by === column.key">
                <chevron-up-icon
                  v-if="params.sort_desc == 0"
                  class="icon icon-sm icon-thick"
                />
                <chevron-down-icon
                  v-else-if="params.sort_desc == 1"
                  class="icon icon-sm icon-thick"
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
        <tr
          v-for="item in items"
          :key="item.id"
        >
          <td
            v-for="column in columns"
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

<script setup lang="ts">
import { useFormatter } from '@/composables/useFormatter'
import { Item, ListParams } from '@/types'
import { OTableColumn } from '@/types'
import { computed } from 'vue'

const { formatDate, formatDateTime, formatTime, formatMoney, formatFileSize } =
  useFormatter()

// props
const props = withDefaults(
  defineProps<{
    data: Array<Item>
    columns: Array<OTableColumn>
    params: ListParams
    stacked?: string
  }>(),
  {
    stacked: 'md',
  }
)

// emits
const emit = defineEmits(['sort'])

// items
const items = computed(() => props.data)

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
</script>
