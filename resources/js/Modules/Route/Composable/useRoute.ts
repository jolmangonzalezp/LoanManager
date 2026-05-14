import { ref } from 'vue'
import type { Route, RouteForm, Zone } from '@/Modules/Route';
import { RouteService } from '@/Modules/Route';
import { UserService } from '@/Modules/User';
import { useNotifier, useModal } from '@/Shared';

const routes = ref<Route[]>([])
const route = ref<Route | null>(null)
const routeForm = ref<RouteForm | null>(null)
const zones = ref<Zone[]>([])
const zoneForm = ref<{ name: string; polygon: string } | null>(null)
const users = ref<{ id: string; name: string }[]>([])
const routeUsers = ref<{ id: string; name: string }[]>([])

export function useRoute() {
  const notify = useNotifier()
  const modal = useModal()

  const columns = [
    { key: 'name', label: 'Nombre' },
    { key: 'zoneName', label: 'Zona' },
    { key: 'userCount', label: 'Usuarios Asignados' },
  ]

  const summary = ref({ total: 0 })

  const updateSummary = () => {
    summary.value = { total: routes.value.length }
  }

  const emptyRoute = () => {
    routeForm.value = { name: '', zoneId: '' }
  }

  const fillRoute = () => {
    if (!route.value) return
    routeForm.value = {
      name: route.value.name,
      zoneId: route.value.zoneId,
    }
  }

  const getAll = async (): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      const response = await RouteService.getAll()
      const zoneList = await RouteService.getZones()
      zones.value = zoneList
      const zoneMap = new Map(zoneList.map(z => [z.id, z.name]))
      routes.value = response.map(r => ({
        ...r,
        zoneName: zoneMap.get(r.zoneId) || '—',
      }))
      updateSummary()
      notify.closeLoading()
    } catch (e: any) {
      notify.closeLoading()
      notify.error('Error', e.message)
    }
  }

  const getZones = async (): Promise<void> => {
    try {
      zones.value = await RouteService.getZones()
    } catch {
      zones.value = []
    }
  }

  const emptyZone = () => {
    zoneForm.value = { name: '', polygon: '' }
  }

  const fillZone = (id: string) => {
    const z = zones.value.find(z => z.id === id)
    if (!z) return
    zoneForm.value = {
      name: z.name,
      polygon: JSON.stringify(z.polygon),
    }
  }

  const getAllZones = async (): Promise<void> => {
    try {
      const response = await RouteService.getZones()
      zones.value = response
    } catch {
      zones.value = []
    }
  }

  const createZone = async (name: string, polygon: number[][]): Promise<boolean> => {
    notify.loading('Cargando', '')
    try {
      const response = await RouteService.createZone(name, polygon)
      if (response) {
        await getAllZones()
      }
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Zona creada exitosamente')
      return response
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
      return false
    }
  }

  const updateZone = async (id: string, name: string, polygon: number[][]): Promise<boolean> => {
    notify.loading('Cargando', '')
    try {
      const response = await RouteService.updateZone(id, name, polygon)
      if (response) {
        await getAllZones()
      }
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Zona actualizada exitosamente')
      return response
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
      return false
    }
  }

  const removeZone = async (id: string): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await RouteService.deleteZone(id)
      await getAllZones()
      notify.closeLoading()
      notify.success('Éxito', 'Zona eliminada exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  const getById = async (id: string): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      route.value = await RouteService.getById(id)
      const zoneList = await RouteService.getZones()
      zones.value = zoneList
      const zoneMap = new Map(zoneList.map(z => [z.id, z.name]))
      if (route.value) {
        route.value.zoneName = zoneMap.get(route.value.zoneId) || '—'
      }
      const allUsers = await UserService.getAll()
      const mapped = allUsers.map(u => ({
        id: u.id,
        name: u.name
          ? [u.name.firstName, u.name.middleName, u.name.lastName, u.name.secondLastName].filter(Boolean).join(' ')
          : u.username,
      }))
      users.value = mapped
      if (route.value) {
        routeUsers.value = mapped.filter(u => route.value!.userIds.includes(u.id))
      }
      notify.closeLoading()
    } catch (e: any) {
      notify.closeLoading()
      notify.error('Error', e.message)
    }
  }

  const create = async (data: RouteForm): Promise<boolean> => {
    notify.loading('Cargando', '')
    try {
      const response = await RouteService.create(data)
      if (response) {
        await getAll()
      }
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Ruta creada exitosamente')
      return response
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
      return false
    }
  }

  const update = async (id: string, data: RouteForm): Promise<boolean> => {
    notify.loading('Cargando', '')
    try {
      const response = await RouteService.update(id, data)
      if (response) {
        await getAll()
      }
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Ruta actualizada exitosamente')
      return response
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
      return false
    }
  }

  const remove = async (id: string): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await RouteService.delete(id)
      await getAll()
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Ruta eliminada exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  const assignUsers = async (id: string, userIds: string[]): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await RouteService.assignUsers(id, userIds)
      await getById(id)
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Usuarios asignados exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  const removeUser = async (routeId: string, userId: string): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await RouteService.removeUser(routeId, userId)
      await getById(routeId)
      notify.closeLoading()
      notify.success('Éxito', 'Usuario removido de la ruta')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  return {
    routes,
    route,
    routeForm,
    zones,
    zoneForm,
    users,
    routeUsers,
    columns,
    summary,
    getAll,
    getById,
    getZones,
    getAllZones,
    create,
    update,
    remove,
    assignUsers,
    removeUser,
    emptyRoute,
    fillRoute,
    emptyZone,
    fillZone,
    createZone,
    updateZone,
    removeZone,
  }
}
