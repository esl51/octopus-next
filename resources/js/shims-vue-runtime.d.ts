/* eslint-disable */
export {}

import '@vue/runtime-core'
import { AppConfig as AppConfigType } from '@/types'

import components from './components'

declare module '@vue/runtime-core' {
  export interface ComponentCustomProperties {
    $appConfig: AppConfigType
  }

  type Components = {
    [key in keyof typeof components]: typeof components[key]
  }

  export interface GlobalComponents extends Components {}
}
