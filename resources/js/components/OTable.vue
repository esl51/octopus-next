<template>
  <div class="table-responsive">
    <table class="table card-table table-vcenter text-nowrap datatable">
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
          >
            <slot
              :name="`cell(${column.key})`"
              :value="item[column.key]"
              :item="item"
            >
              {{ item[column.key] }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { Item, ListParams } from '@/api/items'
import { OTableColumn } from '@/types'
import { computed } from 'vue'

// props
const props = defineProps<{
  data: Array<Item>
  columns: Array<OTableColumn>
  params: ListParams
}>()

// emits
const emit = defineEmits(['sort'])

// items
const items = computed(() => props.data)

// sort
const sort = (key: string) => {
  emit('sort', key)
}
</script>