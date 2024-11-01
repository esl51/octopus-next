<template>
  <div>
    <ul
      v-if="items.length"
      v-sortable="!!sortable ? {} : false"
      class="list-group"
      v-bind="$attrs"
      @drag-end="move"
    >
      <li
        v-for="item in items"
        :key="item.id"
        class="list-group-item p-2"
      >
        <div class="d-flex align-items-center min-w-0">
          <!-- eslint-disable sonarjs/no-vue-bypass-sanitization -->
          <a
            v-tooltip
            :title="$t('global.view')"
            :aria-label="$t('global.view')"
            :href="sanitizeUrl(item.url)"
            target="_blank"
          >
            <o-file-image
              :file="item"
              class="flex-shrink-0 me-2"
              size="md"
            />
          </a>
          <!-- eslint-enable -->
          <div class="flex-fill min-w-0">
            <div class="fw-normal text-truncate">{{ item.title }}</div>
            <small class="text-muted">{{ formatFileSize(item.size) }}</small>
          </div>
          <a
            v-tooltip
            :title="$t('global.download')"
            :aria-label="$t('global.download')"
            href="#"
            @click.prevent="download(item)"
          >
            <o-icon
              :type="IconDownload"
              class="me-2"
            />
          </a>
          <a
            v-tooltip
            :title="$t('global.edit')"
            :aria-label="$t('global.edit')"
            href="#"
            @click.prevent="edit(item)"
          >
            <o-icon
              :type="IconPencil"
              class="me-2"
            />
          </a>
          <a
            v-tooltip
            :title="$t('global.delete')"
            :aria-label="$t('global.delete')"
            :disabled="!item.is_deletable"
            :class="item.is_deletable ? 'text-danger' : 'text-muted pe-none'"
            href="#"
            @click.prevent="destroyFile(item)"
          >
            <o-icon
              :type="IconTrash"
              class="me-1"
            />
          </a>
        </div>
      </li>
    </ul>
    <teleport to="body">
      <o-modal
        ref="modal"
        :title="(current.title as string) ?? $t('files.new_title')"
        @hide="cleanRoute"
      >
        <v-form
          :form="form"
          @submit="submit"
        >
          <v-input
            name="title"
            :label="$t('global.title_label')"
            translatable
          />
          <v-input
            name="original_name"
            :label="$t('files.original_name')"
          />
          <template #footer>
            <o-button
              variant="link"
              class="link-secondary me-2"
              @click="modal?.hide()"
            >
              {{ $t('global.cancel') }}
            </o-button>
            <v-submit class="ms-auto">
              {{ $t('global.save') }}
            </v-submit>
          </template>
        </v-form>
      </o-modal>
    </teleport>
  </div>
</template>

<script setup lang="ts">
import { filesApi } from '@/modules/files/api'
import OModal from '@/components/OModal.vue'
import { useItems } from '@/composables/useItems'
import { File } from '@/modules/files/types'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '@/api'
import { useFormatter } from '@/composables/useFormatter'
import { sanitizeUrl } from '@braintree/sanitize-url'
import { IconTrash, IconDownload, IconPencil } from '@tabler/icons-vue'

const { availableLocales } = useI18n()

const { formatFileSize } = useFormatter()

// props
const props = withDefaults(
  defineProps<{
    filableId: number
    filableType: string
    type: string
    sortable?: boolean
  }>(),
  {
    sortable: false,
  },
)

// modal
const modal = ref<typeof OModal | null>(null)

// defaults
const defaults = {
  original_name: '',
} as Record<string, unknown>
availableLocales.forEach((locale) => {
  defaults['title:' + locale] = ''
})

// items
const { items, form, current, move, edit, submit, destroy, cleanRoute } =
  useItems<File>({
    api: filesApi,
    defaults,
    modal,
    listKey: 'files',
    params: {
      filable_id: props.filableId?.toString(),
      filable_type: props.filableType,
      type: props.type,
    },
  })

// emits
const emit = defineEmits(['delete'])

// download
const download = async (file: File) => {
  const { data } = await api({
    url: file.url,
    method: 'get',
    responseType: 'blob',
  })
  const blob = new Blob([data], { type: file.mime_type })
  const link = document.createElement('a')
  link.href = window.URL.createObjectURL(blob)
  link.download = file.original_name
  document.body.appendChild(link)
  link.click()
  setTimeout(() => {
    document.body.removeChild(link)
  }, 100)
}

// destroy
const destroyFile = async (file: File) => {
  await destroy(file, file.title ?? file.id.toString())
  emit('delete')
}
</script>
