import Tooltip from 'bootstrap/js/dist/tooltip'

export const tooltip = {
  mounted(el: HTMLElement) {
    new Tooltip(el)
  },
}

export default tooltip
