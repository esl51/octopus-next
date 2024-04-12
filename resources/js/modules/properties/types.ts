import { Item } from '@/types'

export interface PropertyType {
  id: number
  alias: string
  name: string
  component: string | null
  validator: string | null
  column: string | null
  is_multiple: boolean
  has_values: boolean
  has_files: boolean
}

export interface PropertyGroup extends Item {
  name: string
}

export interface Property extends Item {
  name: string
  alias: string
  property_type_id: number
  property_group_id: number | null
  property_type: PropertyType
  property_group: PropertyGroup
}

export interface PropertyValue extends Item {
  name: string
}
