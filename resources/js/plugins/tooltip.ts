import { Tooltip } from 'bootstrap'

export const tooltip = {
  mounted(el: HTMLElement) {
    new Tooltip(el)
  },
}

export default tooltip
