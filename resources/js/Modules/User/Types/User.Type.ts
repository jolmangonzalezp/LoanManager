export interface User {
  id: string
  username: string
  name: string
  email: string
  phone: string
  createdAt: string
  enabled: boolean
}

export interface UserForm {
  username: string
  password?: string
  name: string
  email: string
  phone: string
}

export interface Role {
  id: string
  slug: string
  name: string
  description: string
  isSystem: boolean
  createdAt: string
}

export interface RoleForm {
  slug: string
  name: string
  description: string
  permissions: string[]
}

export interface Permission {
  id: string
  slug: string
  name: string
  description: string
  group: string
  createdAt: string
}

export interface UserPermissions {
  permissions: string[]
}
