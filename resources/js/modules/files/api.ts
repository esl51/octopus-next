import itemsApi from '@/api/items'
import { ItemsApi } from '@/types'
import { File } from './types'

export const filesUrl = '/api/files'

export const filesApi: ItemsApi<File> = itemsApi(filesUrl)
