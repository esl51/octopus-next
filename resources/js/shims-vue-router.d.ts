export {}

import 'vue-router'

declare module 'vue-router' {
  interface RouteMeta {
    layout?: string
    middleware?: string | Array<string>
    permissions?: string | Array<string>
  }
}
