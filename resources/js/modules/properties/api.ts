import api from '@/api'
import itemsApi from '@/api/items'
import { ItemsApi } from '@/types'
import { Property, PropertyGroup } from './types'

export const propertiesUrls = {
  propertyTypes: '/api/property-types',
  propertyGroups: '/api/property-groups',
  properties: '/api/properties',
}

export const propertyGroupsApi: ItemsApi<PropertyGroup> = itemsApi(
  propertiesUrls.propertyGroups,
)
export const propertiesApi: ItemsApi<Property> = itemsApi(
  propertiesUrls.properties,
)

export const propertyTypesList = async () => {
  const { data } = await api.get(propertiesUrls.propertyTypes)
  return data.data
}
