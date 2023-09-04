import '../sass/app.scss'
import App from './App.vue'
import components from './components'
import head from './plugins/head'
import i18n from './plugins/i18n'
import pinia from './plugins/pinia'
import sortable from './plugins/sortable'
import tooltip from './plugins/tooltip'
import router from './router'
import 'bootstrap'
import { createApp } from 'vue'
import Vue3Mount from 'vue3-mount'

const app = createApp(App)

// plugins
app.use(pinia)
app.use(router)
app.use(head)
app.use(i18n)
app.use(Vue3Mount)

// directives
app.directive('sortable', sortable)
app.directive('tooltip', tooltip)

// globals
app.config.globalProperties.$appConfig = window.config

// components
Object.entries(components).forEach(([name, definition]) => {
  app.component(name, definition)
})

// mount
app.mount('#app')
