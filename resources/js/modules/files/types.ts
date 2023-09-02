import { Item } from '@/types'

export interface File extends Item {
  type?: string
  original_name: string
  size: number
  mime_type: string
  extension: string
  title?: string
  url: string
}
