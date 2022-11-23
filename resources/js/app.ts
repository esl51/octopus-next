import '../sass/app.scss'
import App from './App.vue'
import components from './components'
import head from './plugins/head'
import i18n from './plugins/i18n'
import pinia from './plugins/pinia'
import router from './router'
import 'bootstrap'
import { BootstrapVue3 } from 'bootstrap-vue-3'
import { createApp } from 'vue'
import Vue3Mount from 'vue3-mount'

const app = createApp(App)

// plugins
app.use(pinia)
app.use(router)
app.use(head)
app.use(i18n)
app.use(BootstrapVue3)
app.use(Vue3Mount)

// globals
app.config.globalProperties.$appConfig = window.config

// components
Object.entries(components).forEach(([name, definition]) => {
  app.component(name, definition)
})

// mount
app.mount('#app')
