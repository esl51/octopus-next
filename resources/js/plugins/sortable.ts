import Sortable from 'sortablejs'
import { DirectiveBinding, ObjectDirective, VNode } from 'vue'

const createSortable = (
  el: HTMLElement,
  options: DirectiveBinding,
  vnode: VNode,
) => {
  return Sortable.create(el, {
    ...options,
    onEnd(event: Event) {
      if (vnode.props?.onDragEnd) {
        vnode.props.onDragEnd(event)
      }
    },
  })
}

interface SortableHTMLElement extends HTMLElement {
  _sortable?: Sortable
}

export const sortable: ObjectDirective<SortableHTMLElement> = {
  mounted(el: SortableHTMLElement, binding: DirectiveBinding, vnode: VNode) {
    if (binding.value === false) {
      return
    }
    let elem = el.querySelector('tbody') as SortableHTMLElement
    if (!elem) {
      elem = el
    }
    el._sortable = createSortable(elem, binding.value, vnode)
  },
  updated(el: SortableHTMLElement, binding: DirectiveBinding, vnode: VNode) {
    if (binding.value === false) {
      return
    }
    let elem = el.querySelector('tbody') as SortableHTMLElement
    if (!elem) {
      elem = el
    }
    el._sortable?.destroy()
    el._sortable = createSortable(elem, binding.value, vnode)
  },
  unmounted(el: SortableHTMLElement, binding: DirectiveBinding) {
    if (binding.value === false) {
      return
    }
    el._sortable?.destroy()
  },
}

export default sortable
