import api from '.'
import { Item, ItemApi } from '@/types'

/**
 * Item Api
 * @param endpoint Api endpoint URL
 * @returns Item Api methods
 */
export default function itemApi<T extends Item>(endpoint: string): ItemApi<T> {
  const url = endpoint.replace(/\/$/, '') + '/'

  /**
   * Get single item.
   * @param id Item id
   * @returns Item
   */
  const get = async (id: number): Promise<T> => {
    const { data } = await api.get(url + id)
    return data.data as T
  }

  /**
   * Update item.
   * @param id Item id
   * @param payload Item data
   * @returns Item
   */
  const update = async (
    id: number,
    payload: Record<string, unknown>,
  ): Promise<T> => {
    const { data } = await api.put(url + id, payload)
    return data.data as T
  }

  /**
   * Delete item.
   * @param id Item id
   */
  const destroy = async (id: number): Promise<void> => {
    await api.delete(url + id)
  }

  return {
    url,
    get,
    update,
    destroy,
  }
}
