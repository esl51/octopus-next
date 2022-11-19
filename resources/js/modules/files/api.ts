import itemsApi from '@/api/items'
import { ItemsApi } from '@/types'

export const filesUrl = '/api/files'

export const filesApi: ItemsApi = itemsApi(filesUrl)
