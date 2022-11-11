import api from './'

export interface ListParams {
  id?: number
  search?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_desc?: number
  [key: string]: unknown
}

export interface Item {
  id: number
  is_deletable: boolean
  is_editable: boolean
  created_at: Date
  updated_at: Date
  [key: string]: unknown
}

export interface Meta {
  current_page: number
  from: number
  last_page: number
  per_page: number
  to: number
  total: number
}

export interface ListResponse {
  data: Array<Item>
  meta: Meta
}

export interface ItemsApi {
  url: string
  list: (params?: ListParams) => Promise<ListResponse>
  get: (id: number) => Promise<Item>
  store: (payload: Record<string, unknown>) => Promise<Item>
  update: (id: number, payload: Record<string, unknown>) => Promise<Item>
  destroy: (id: number) => Promise<void>
}

/**
 * Items Api
 * @param endpoint Api endpoint URL
 * @returns Items Api methods
 */
export default function itemsApi(endpoint: string): ItemsApi {
  const url = endpoint.replace(/\/*$/, '/')

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
    payload: Record<string, unknown>
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

  return {
    url,
    list,
    get,
    store,
    update,
    destroy,
  }
}
