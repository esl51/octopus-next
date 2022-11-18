<template>
  <v-form-control>
    <template #labelDescription="slotData: any">
      <slot
        name="labelDescription"
        v-bind="slotData"
      />
      <v-locale-switch
        v-if="translatable"
        v-model="activeLocale"
        :states="localeStates"
      />
    </template>
    <jodit-editor
      v-for="locale in translatable ? availableLocales : [undefined]"
      v-show="!translatable || locale === activeLocale"
      :id="locale ? id + '-' + locale : id"
      :key="locale ? name + ':' + locale : name"
      v-model="model[locale ? name + ':' + locale : name]"
      :name="locale ? name + ':' + locale : name"
      :config="{
        autofocus,
        disabled,
        readonly,
        placeholder: placeholder ?? '',
        editorClassName: autofocus ? 'autofocus' : null,
        className: state === false ? 'is-invalid' : null,
        hidePoweredByJodit: true,
        controls: {
          paragraph: {
            list: {
              p: 'Normal',
            },
          },
        },
        toolbarAdaptive: false,
        defaultActionOnPaste: 'insert_clear_html',
        buttons: [
          'undo',
          'redo',
          'paragraph',
          '|',
          'bold',
          'italic',
          'underline',
          'strikethrough',
          'align',
          '|',
          'table',
          'link',
          'hr',
          '|',
          'ul',
          'ol',
          '|',
          'superscript',
          'subscript',
          '|',
          'image',
          'file',
          'video',
          '|',
          'eraser',
          'fullsize',
          'source',
        ],
        events: {
          getIcon: (name: string, control: string, clearName: string) => {
            if (icons[clearName]) {
              return icons[clearName]
            }
          }
        }
      }"
    />
  </v-form-control>
</template>

<script setup lang="ts">
import JoditEditor from './vendor/JoditEditor.vue'
import { useFormControl } from '@/composables/useFormControl'
import Form from 'vform'
import { computed, inject, ref } from 'vue'

const icons = ref({} as { [key: string]: string })
const iconNames: { [key: string]: string } = {
  'rotate-2': 'undo',
  'rotate-clockwise-2': 'redo',
  'section-sign': 'paragraph',
  'align-left': 'left',
  'align-right': 'right',
  'align-center': 'center',
  'align-justified': 'justify',
  typography: 'font',
  'text-size': 'fontsize',
  shadow: 'classSpan',
  'line-height': 'lineHeight',
  'text-spellcheck': 'spellcheck',
  'clipboard-copy': 'paste',
  'box-margin': 'select_all',
  paint: 'copyformat',
  minus: 'hr',
  omega: 'symbols',
  'indent-increase': 'indent',
  'indent-decrease': 'outdent',
  list: 'ul',
  'list-numbers': 'ol',
  photo: 'image',
  maximize: 'fullsize',
  code: 'source',
  droplet: 'brush',
  'arrows-vertical': 'valign',
  'layout-board-split': 'splitv',
  'arrows-diagonal-minimize': 'merge',
  'column-insert-right': 'addcolumn',
  'row-insert-bottom': 'addrow',
  trash: 'bin',
  printer: 'print',
  'question-mark': 'about',
  'chevron-down': 'chevron',
}
Object.entries(
  import.meta.glob(
    '../../../node_modules/@tabler/icons/icons/(rotate-2|rotate-clockwise-2|search|section-sign|bold|italic|underline|strikethrough|align-left|align-right|align-center|align-justified|typography|text-size|shadow|line-height|text-spellcheck|cut|copy|clipboard-copy|box-margin|paint|table|link|minus|omega|indent-increase|indent-decrease|list|list-numbers|superscript|subscript|photo|file|video|eraser|maximize|code|droplet|arrows-vertical|layout-board-split|column-insert-right|arrows-diagonal-minimize|row-insert-bottom|trash|eye|printer|question-mark|chevron-down|pencil|unlink).svg',
    { eager: true, as: 'raw' }
  )
).forEach(([path, definition]) => {
  const name = path
    .split('/')
    .reverse()
    .shift()
    ?.replace(/\.svg$/, '')
  if (name && definition) {
    icons.value[iconNames[name] || name] = definition
  }
})

// props
const props = withDefaults(
  defineProps<{
    modelValue?: Form
    name: string
    label?: string
    hint?: string
    disabled?: boolean
    readonly?: boolean
    autofocus?: boolean
    placeholder?: string
    translatable?: boolean
  }>(),
  {
    modelValue: undefined,
    label: undefined,
    hint: undefined,
    disabled: false,
    readonly: false,
    autofocus: false,
    placeholder: undefined,
    translatable: false,
  }
)

// control
const { id, state, activeLocale, availableLocales, localeStates } =
  useFormControl(props)

// form
const form: Form | undefined = inject('form')

// value
const emit = defineEmits(['update:modelValue'])
const model = computed({
  get: () => props.modelValue || form?.value,
  set: (value) => {
    emit('update:modelValue', value)
  },
})
</script>
