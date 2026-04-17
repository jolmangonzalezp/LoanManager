<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../../composables/useApi'

const props = defineProps({
  currentRoute: { type: String, default: 'dashboard' },
  collapsed: { type: Boolean, default: false }
})

const emit = defineEmits(['navigate', 'newLoan', 'newCustomer', 'newPayment', 'toggle'])
const router = useRouter()
const api = useApi()

const user = ref({ name: 'Admin', email: '' })

const mainMenuItems = [
  { id: 'dashboard', label: 'Inicio', icon: '◈' },
  { id: 'clientes', label: 'Clientes', icon: '⊕' },
  { id: 'prestamos', label: 'Cartera', icon: '◎' },
  { id: 'pagos', label: 'Pagos', icon: '◉' },
]

const reportsMenuItems = [
  { path: '/reportes', label: 'Resumen', icon: '📊' },
  { path: '/reportes/cartera', label: 'Cartera', icon: '💰' },
  { path: '/reportes/rentabilidad', label: 'Rentabilidad', icon: '📈' },
  { path: '/reportes/mora', label: 'Mora', icon: '⚠️' },
  { path: '/reportes/prestamos-activos', label: 'Préstamos Activos', icon: '📋' },
  { path: '/reportes/flujo-caja', label: 'Flujo de Caja', icon: '💸' },
  { path: '/reportes/historial-pagos', label: 'Historial de Pagos', icon: '💳' },
  { path: '/reportes/auditoria', label: 'Auditoría', icon: '📝' },
]

const showReportsSubmenu = ref(false)

function logout() {
  localStorage.removeItem('token')
  router.push('/login')
}

function toggleSidebar() {
  emit('toggle')
}

function navigateTo(id) {
  if (id === 'reportes') {
    showReportsSubmenu.value = !showReportsSubmenu.value
  } else {
    showReportsSubmenu.value = false
    emit('navigate', id)
  }
}

function navigateToReport(path) {
  showReportsSubmenu.value = false
  router.push(path)
}

function isMainActive(id) {
  if (id === 'reportes') return props.currentRoute === 'reportes'
  return props.currentRoute === id
}

function isReportActive(path) {
  if (path === '/reportes') return window.location.pathname === '/reportes'
  return window.location.pathname.startsWith(path)
}

onMounted(async () => {
  try {
    const data = await api.get('/auth/me')
    user.value = data
  } catch (e) {
    console.error('Error loading user:', e)
  }
})
</script>

<template>
  <aside class="sidebar" :class="{ collapsed }">
    <nav class="nav">
      <div 
        v-for="item in mainMenuItems" 
        :key="item.id"
        class="nav-item"
        :class="{ active: isMainActive(item.id) }"
        @click="navigateTo(item.id)"
      >
        <span class="nav-icon">{{ item.icon }}</span>
        <span class="nav-label">{{ item.label }}</span>
      </div>

      <div 
        class="nav-item"
        :class="{ active: isMainActive('reportes') }"
        @click="navigateTo('reportes')"
      >
        <span class="nav-icon">▤</span>
        <span class="nav-label">Reportes</span>
        <span class="nav-arrow" :class="{ rotated: showReportsSubmenu }">▶</span>
      </div>

      <div v-if="showReportsSubmenu && !collapsed" class="submenu">
        <div 
          v-for="item in reportsMenuItems" 
          :key="item.path"
          class="submenu-item"
          :class="{ active: isReportActive(item.path) }"
          @click="navigateToReport(item.path)"
        >
          <span class="submenu-icon">{{ item.icon }}</span>
          <span class="submenu-label">{{ item.label }}</span>
        </div>
      </div>
    </nav>
    <div class="sidebar-footer">
      <div class="user" :class="{ hidden: collapsed }">
        <span class="user-avatar">JM</span>
        <div class="user-info">
          <div class="user-name">Admin</div>
          <div class="user-role">Administrador</div>
        </div>
      </div>
      <button class="logout-btn" :class="{ hidden: collapsed }" @click="logout">Cerrar Sesión</button>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  width: 220px;
  min-height: calc(100vh - 56px);
  background: rgba(0,0,0,0.3);
  border-right: 1px solid rgba(255,255,255,0.07);
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  position: fixed;
  top: 56px;
  left: 0;
  flex-shrink: 0;
  z-index: 200;
}

.sidebar.collapsed {
  width: 64px;
}

.nav {
  flex: 1;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
  color: #94a3b8;
  font-size: 13px;
  white-space: nowrap;
  overflow: hidden;
}

.sidebar.collapsed .nav-item {
  justify-content: center;
  padding: 12px;
}

.nav-item:hover {
  background: rgba(212,175,55,0.05);
  color: #e8c84a;
}

.nav-item.active {
  background: rgba(212,175,55,0.1);
  color: #d4af37;
  border-left: 2px solid #d4af37;
}

.sidebar.collapsed .nav-item.active {
  border-left: none;
  border-bottom: 2px solid #d4af37;
}

.nav-icon {
  font-size: 16px;
  flex-shrink: 0;
  width: 20px;
  text-align: center;
}

.nav-label {
  flex: 1;
  transition: opacity 0.2s ease;
}

.nav-arrow {
  font-size: 10px;
  transition: transform 0.2s ease;
  color: #64748b;
}

.nav-arrow.rotated {
  transform: rotate(90deg);
}

.sidebar.collapsed .nav-label {
  opacity: 0;
  width: 0;
}

.sidebar.collapsed .nav-arrow {
  display: none;
}

.submenu {
  padding-left: 20px;
  display: flex;
  flex-direction: column;
  gap: 2px;
  margin-top: 4px;
  margin-bottom: 8px;
}

.submenu-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
  color: #64748b;
  font-size: 12px;
}

.submenu-item:hover {
  background: rgba(255,255,255,0.03);
  color: #94a3b8;
}

.submenu-item.active {
  background: rgba(212,175,55,0.08);
  color: #d4af37;
}

.submenu-icon {
  font-size: 14px;
  flex-shrink: 0;
  width: 18px;
  text-align: center;
}

.submenu-label {
  white-space: nowrap;
}

.sidebar-footer {
  padding: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.user {
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s ease;
}

.user.hidden {
  opacity: 0;
  height: 0;
  overflow: hidden;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: rgba(212,175,55,0.1);
  border: 1px solid rgba(212,175,55,0.28);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  color: #d4af37;
  font-family: 'Share Tech Mono', monospace;
  flex-shrink: 0;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 13px;
  font-weight: 600;
  color: #fff;
}

.user-role {
  font-size: 11px;
  color: #94a3b8;
}

.logout-btn {
  width: 100%;
  padding: 8px;
  background: transparent;
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 6px;
  color: #94a3b8;
  font-size: 11px;
  cursor: pointer;
  transition: all 0.15s;
  white-space: nowrap;
}

.logout-btn:hover {
  background: rgba(239,68,68,0.1);
  border-color: rgba(239,68,68,0.3);
  color: #ef4444;
}

.logout-btn.hidden {
  opacity: 0;
  height: 0;
  padding: 0;
  border: none;
  overflow: hidden;
}
</style>
