import api from '.'
import { Item, ItemsApi, ListParams, ListResponse } from '@/types'

/**
 * Items Api
 * @param endpoint Api endpoint URL
 * @returns Items Api methods
 */
export default function itemsApi(endpoint: string): ItemsApi {
  const url = endpoint.replace(/\/*$/, '/')

  /**
   * All items.
   * @returns ListResponse
   */
  const all = async (): Promise<ListResponse> => {
    return await list({ per_page: 999 })
  }

  /**
   * List items.
   * @param params Query params
   * @returns ListResponse
   */
  const list = async (params?: ListParams): Promise<ListResponse> => {
    const { data } = await api.get(url, { params })
    return data as ListResponse
  }

  /**
   * Get single item.
   * @param id Item id
   * @returns Item
   */
  const get = async (id: number): Promise<Item> => {
    const { data } = await api.get(url + id)
    return data.data as Item
  }

  /**
   * Store item.
   * @param payload Item data
   * @returns Item
   */
  const store = async (payload: Record<string, unknown>): Promise<Item> => {
    const { data } = await api.post(url, payload)
    return data.data as Item
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
  ): Promise<Item> => {
    const { data } = await api.put(url + id, payload)
    return data.data as Item
  }

  /**
   * Delete item.
   * @param id Item id
   */
  const destroy = async (id: number): Promise<void> => {
    await api.delete(url + id)
  }

  /**
   * Move item after id
   * @param id Item id
   * @param afterId Item after id
   * @returns Item
   */
  const moveAfter = async (id: number, afterId: number): Promise<Item> => {
    const { data } = await api.post(url + id + '/move-after/' + afterId)
    return data.data as Item
  }

  /**
   * Move item before id
   * @param id Item id
   * @param beforeId Item before id
   * @returns Item
   */
  const moveBefore = async (id: number, beforeId: number): Promise<Item> => {
    const { data } = await api.post(url + id + '/move-before/' + beforeId)
    return data.data as Item
  }

  return {
    url,
    all,
    list,
    get,
    store,
    update,
    destroy,
    moveAfter,
    moveBefore,
  }
}
