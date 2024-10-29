<template>
  <ul
    v-if="fileList?.length"
    class="list-group"
  >
    <li
      v-for="item in fileList"
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
          <div class="fw-medium text-truncate">{{ item.title }}</div>
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
          :title="$t('global.delete')"
          :aria-label="$t('global.delete')"
          :disabled="!item.is_deletable"
          :class="item.is_deletable ? 'text-danger' : 'text-muted pe-none'"
          href="#"
          @click.prevent="destroy(item)"
        >
          <o-icon
            :type="IconTrash"
            class="me-1"
          />
        </a>
      </div>
    </li>
  </ul>
</template>

<script setup lang="ts">
import api from '@/api'
import { useConfirm } from '@/composables/useConfirm'
import { useFormatter } from '@/composables/useFormatter'
import { filesApi } from '@/modules/files/api'
import { File } from '@/modules/files/types'
import { sanitizeUrl } from '@braintree/sanitize-url'
import { IconTrash, IconDownload } from '@tabler/icons-vue'
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const { confirm } = useConfirm()

const { formatFileSize } = useFormatter()

// props
const props = withDefaults(
  defineProps<{
    files?: Record<string, unknown> | Array<Record<string, unknown>>
    confirmable?: boolean
  }>(),
  {
    files: undefined,
    confirmable: false,
  },
)

// emits
const emit = defineEmits(['delete'])

// file list
const fileList = ref<Array<File>>([])

// watch
watch(
  () => props.files,
  (files) => {
    fileList.value = []
    if (files) {
      fileList.value = Array.isArray(files)
        ? (files as Array<File>)
        : [files as File]
    }
  },
  { immediate: true },
)

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
const destroy = async (file: File) => {
  if (
    props.confirmable === false ||
    (await confirm({
      title: t('global.confirm_title'),
      body: t('global.confirm_delete_body', {
        name: file.title ?? file.id,
      }),
      yesVariant: 'danger',
      yesTitle: t('global.yes_delete'),
    }))
  ) {
    const index = fileList.value.map((f) => f.id).indexOf(file.id)
    await filesApi.destroy(file.id)
    fileList.value.splice(index, 1)
    emit('delete')
  }
}
</script>
