import { Item } from '@/types'
import { File } from '@/modules/files/types'

export interface Role extends Item {
  id: number
  name: string
  title: string
}

export interface Permission extends Item {
  id: number
  name: string
}

export interface User extends Item {
  id: number
  name: string
  email: string
  email_verified_at: Date | null
  can?: { [permission: string]: boolean }
  roles?: Array<Role>
  name_placeholder: string
  avatar?: File
}
