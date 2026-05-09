import { ref } from 'vue'
import type { User, UserForm, Role, Permission } from '@/Modules/User';
import { UserService } from '@/Modules/User';
import { useNotifier, useModal } from '@/Shared';

const users = ref<User[]>([])
const user = ref<User | null>(null)
const userForm = ref<UserForm | null>(null)
const roles = ref<Role[]>([])
const permissions = ref<Permission[]>([])
const userPermissions = ref<string[]>([])
const userRoles = ref<string[]>([])
const allRoles = ref<Role[]>([])
const allPermissions = ref<Permission[]>([])

export function useUser() {
  const notify = useNotifier()
  const modal = useModal()

  const columns = [
    { key: 'username', label: 'Usuario' },
    { key: 'name', label: 'Nombre' },
    { key: 'email', label: 'Email' },
    { key: 'phone', label: 'Teléfono' },
    { key: 'enabled', label: 'Estado' },
  ]

  const summary = ref({ total: 0, enabled: 0, disabled: 0 })

  const updateSummary = () => {
    summary.value = {
      total: users.value.length,
      enabled: users.value.filter(u => u.enabled).length,
      disabled: users.value.filter(u => !u.enabled).length,
    }
  }

  const emptyUser = () => {
    userForm.value = { username: '', name: '', email: '', phone: '' }
  }

  const fillUser = () => {
    if (!user.value) return
    userForm.value = {
      username: user.value.username,
      name: user.value.name,
      email: user.value.email,
      phone: user.value.phone,
    }
  }

  const getAll = async (): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      users.value = await UserService.getAll()
      updateSummary()
      notify.closeLoading()
    } catch (e: any) {
      notify.closeLoading()
      notify.error('Error', e.message)
    }
  }

  const getById = async (id: string): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      user.value = await UserService.getById(id)
      notify.closeLoading()
    } catch (e: any) {
      notify.closeLoading()
      notify.error('Error', e.message)
    }
  }

  const create = async (data: UserForm): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await UserService.create(data)
      await getAll()
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Usuario creado exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  const update = async (id: string, data: Partial<UserForm>): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await UserService.update(id, data)
      await getAll()
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Usuario actualizado exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  const remove = async (id: string): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await UserService.delete(id)
      await getAll()
      notify.closeLoading()
      notify.success('Éxito', 'Usuario deshabilitado exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  const assignRoles = async (id: string, roleSlugs: string[]): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await UserService.assignRoles(id, roleSlugs)
      await getPermissions(id)
      await getRoles(id)
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Roles asignados exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  const getPermissions = async (id: string): Promise<void> => {
    try {
      userPermissions.value = await UserService.getPermissions(id)
    } catch {
      userPermissions.value = []
    }
  }

  const getRoles = async (id: string): Promise<void> => {
    try {
      userRoles.value = await UserService.getRoles(id)
    } catch {
      userRoles.value = []
    }
  }

  const loadRoles = async (): Promise<void> => {
    try {
      allRoles.value = await UserService.getRolesAll()
    } catch {
      allRoles.value = []
    }
  }

  const loadPermissions = async (): Promise<void> => {
    try {
      allPermissions.value = await UserService.getPermissionsAll()
    } catch {
      allPermissions.value = []
    }
  }

  const createRole = async (data: Record<string, any>): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await UserService.createRole(data)
      await loadRoles()
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Rol creado exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  const updateRole = async (id: string, data: Record<string, any>): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await UserService.updateRole(id, data)
      await loadRoles()
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Rol actualizado exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  const deleteRole = async (id: string): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await UserService.deleteRole(id)
      await loadRoles()
      notify.closeLoading()
      notify.success('Éxito', 'Rol eliminado exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  const createPermission = async (data: Record<string, any>): Promise<void> => {
    notify.loading('Cargando', '')
    try {
      await UserService.createPermission(data)
      await loadPermissions()
      modal.close()
      notify.closeLoading()
      notify.success('Éxito', 'Permiso creado exitosamente')
    } catch (e: any) {
      notify.closeLoading()
      notify.toastError(e.message)
    }
  }

  return {
    users,
    user,
    userForm,
    roles,
    permissions,
    userPermissions,
    userRoles,
    allRoles,
    allPermissions,
    columns,
    summary,
    getAll,
    getById,
    getPermissions,
    getRoles,
    create,
    update,
    remove,
    assignRoles,
    emptyUser,
    fillUser,
    loadRoles,
    loadPermissions,
    createRole,
    updateRole,
    deleteRole,
    createPermission,
  }
}
