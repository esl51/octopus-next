import en from './en'
import ru from './ru'
import { LocaleMessage } from '@intlify/core-base'
import { VueMessageType } from 'vue-i18n'

const messages = {
  en,
  ru,
}

Object.entries(
  import.meta.glob('../modules/*/lang/*.ts', {
    eager: true,
    import: 'default',
  })
).forEach(([path, definition]) => {
  const moduleName = path.split('/')[2]
  const locale = path.split('/').reverse().shift()?.replace(/\.ts$/, '')
  if (locale && Object.keys(messages).includes(locale) && definition) {
    ;(messages as { [x: string]: LocaleMessage<VueMessageType> })[locale][
      moduleName
    ] = definition
  }
})

export default messages
