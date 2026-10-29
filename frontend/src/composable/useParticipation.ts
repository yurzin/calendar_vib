// src/composable/useParticipation.ts
import { ref, watch, toValue, type MaybeRefOrGetter } from 'vue'
import axios from 'axios'

export interface Participation {
  id: number
  name: string
  slug: string
  description: string
  price: string | null
  sold_out?: boolean
}

export function useParticipation(slug: MaybeRefOrGetter<string | undefined>) {
  const participation = ref<Participation | null>(null)
  const loading = ref(false)
  const notFound = ref(false)
  let controller: AbortController | null = null

  const load = async (value: string) => {
    controller?.abort() // отменяем предыдущий запрос при смене slug
    const ctrl = new AbortController()
    controller = ctrl

    loading.value = true
    notFound.value = false
    try {
      const { data } = await axios.get(
        `/api/participation/${encodeURIComponent(value)}`,
        { signal: ctrl.signal }
      )
      participation.value = data.participation
    } catch (e: any) {
      if (axios.isCancel(e)) return
      participation.value = null
      notFound.value = e?.response?.status === 404
    } finally {
      if (ctrl === controller) loading.value = false
    }
  }

  watch(
    () => toValue(slug),
    (value) => { if (value) load(value) },
    { immediate: true }
  )

  return { participation, loading, notFound, reload: load }
}
