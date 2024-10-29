import OActions from './OActions.vue'
import OAlert from './OAlert.vue'
import OAvatar from './OAvatar.vue'
import OButton from './OButton.vue'
import OCard from './OCard.vue'
import OButtonAdd from './OButtonAdd.vue'
import OCollapse from './OCollapse.vue'
import OConfirm from './OConfirm.vue'
import ODropdown from './ODropdown.vue'
import OFileImage from './OFileImage.vue'
import OFileList from './OFileList.vue'
import OIcon from './OIcon.vue'
import OPageHeader from './OPageHeader.vue'
import OPagination from './OPagination.vue'
import OSearch from './OSearch.vue'
import OSpinner from './OSpinner.vue'
import OTable from './OTable.vue'
import OTableFooter from './OTableFooter.vue'
import OToast from './OToast.vue'
import VCheckbox from './VCheckbox.vue'
import VCheckboxes from './VCheckboxes.vue'
import VDatepicker from './VDatepicker.vue'
import VFile from './VFile.vue'
import VForm from './VForm.vue'
import VFormControl from './VFormControl.vue'
import VInput from './VInput.vue'
import VRadios from './VRadios.vue'
import VSelect from './VSelect.vue'
import VSubmit from './VSubmit.vue'
import VTextarea from './VTextarea.vue'
import type { Component } from 'vue'

type ComponentMap = {
  [key: string]: Component
}

const components: ComponentMap = {
  OActions,
  OAlert,
  OAvatar,
  OButton,
  OButtonAdd,
  OCard,
  OCollapse,
  OConfirm,
  ODropdown,
  OFileImage,
  OFileList,
  OIcon,
  OPageHeader,
  OPagination,
  OSearch,
  OSpinner,
  OTable,
  OTableFooter,
  OToast,
  VCheckbox,
  VCheckboxes,
  VDatepicker,
  VFile,
  VForm,
  VFormControl,
  VInput,
  VRadios,
  VSelect,
  VSubmit,
  VTextarea,
}

export default components
