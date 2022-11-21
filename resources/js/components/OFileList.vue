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
        <b-link
          :href="item.url"
          target="_blank"
        >
          <o-file-image
            :file="item"
            class="flex-shrink-0 me-2"
            size="sm"
          />
        </b-link>
        <div class="flex-fill min-w-0">
          <div class="font-weight-medium text-truncate">{{ item.title }}</div>
        </div>
        <b-link @click.prevent="download(item)">
          <download-icon class="icon me-2" />
        </b-link>
        <b-link
          :class="item.is_deletable ? 'text-danger' : 'text-muted'"
          @click.prevent="destroy(item)"
        >
          <trash-icon class="icon me-1" />
        </b-link>
      </div>
    </li>
  </ul>
</template>

<script setup lang="ts">
import api from '@/api'
import { useConfirm } from '@/composables/useConfirm'
import { filesApi } from '@/modules/files/api'
import { File } from '@/types'
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const { confirm } = useConfirm()

// props
const props = withDefaults(
  defineProps<{
    files?: Record<string, unknown> | Array<Record<string, unknown>>
    confirmable?: boolean
  }>(),
  {
    files: undefined,
    confirmable: false,
  }
)

// emits
const emit = defineEmits(['delete'])

// file list
const fileList = ref<Array<File>>([])

// watch
watch(
  () => props.files,
  (files) => {
    fileList.value = files
      ? Array.isArray(files)
        ? (files as Array<File>)
        : [files as File]
      : []
  },
  { immediate: true }
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
