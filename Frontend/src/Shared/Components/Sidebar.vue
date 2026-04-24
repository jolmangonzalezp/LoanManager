<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../../composables/useApi'
import {
  faHome,
  faUsers,
  faMoneyCheckAlt,
  faHandHoldingUsd,
  faChartBar,
  faChartPie,
  faChartLine,
  faExclamationTriangle,
  faFileAlt,
  faFileInvoiceDollar,
  faHistory,
  faClipboardList,
  faChevronRight,
  faSignOutAlt
} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

const props = defineProps({
  currentRoute: { type: String, default: 'dashboard' },
  collapsed: { type: Boolean, default: false }
})

const emit = defineEmits(['navigate', 'newLoan', 'newCustomer', 'newPayment', 'toggle'])
const router = useRouter()
const api = useApi()

const user = ref({ name: 'Admin', email: '' })

const mainMenuItems = [
  { id: 'dashboard', label: 'Inicio', icon: faHome },
  { id: 'clientes', label: 'Clientes', icon: faUsers },
  { id: 'prestamos', label: 'Cartera', icon: faMoneyCheckAlt },
  { id: 'pagos', label: 'Pagos', icon: faHandHoldingUsd },
]

const reportsMenuItems = [
  { path: '/reportes', label: 'Resumen', icon: faChartBar },
  { path: '/reportes/cartera', label: 'Cartera', icon: faChartPie },
  { path: '/reportes/rentabilidad', label: 'Rentabilidad', icon: faChartLine },
  { path: '/reportes/mora', label: 'Mora', icon: faExclamationTriangle },
  { path: '/reportes/prestamos-activos', label: 'Préstamos Activos', icon: faFileAlt },
  { path: '/reportes/flujo-caja', label: 'Flujo de Caja', icon: faFileInvoiceDollar },
  { path: '/reportes/historial-pagos', label: 'Historial de Pagos', icon: faHistory },
  { path: '/reportes/auditoria', label: 'Auditoría', icon: faClipboardList },
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
        <FontAwesomeIcon :icon="item.icon" class="nav-icon" />
        <span class="nav-label">{{ item.label }}</span>
      </div>

      <div 
        class="nav-item"
        :class="{ active: isMainActive('reportes') }"
        @click="navigateTo('reportes')"
      >
        <FontAwesomeIcon :icon="faChartBar" class="nav-icon" />
        <span class="nav-label">Reportes</span>
        <FontAwesomeIcon :icon="faChevronRight" class="nav-arrow" :class="{ rotated: showReportsSubmenu }" />
      </div>

      <div v-if="showReportsSubmenu && !collapsed" class="submenu">
        <div 
          v-for="item in reportsMenuItems" 
          :key="item.path"
          class="submenu-item"
          :class="{ active: isReportActive(item.path) }"
          @click="navigateToReport(item.path)"
        >
          <FontAwesomeIcon :icon="item.icon" class="submenu-icon" />
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
      <button class="logout-btn" :class="{ hidden: collapsed }" @click="logout">
        <FontAwesomeIcon :icon="faSignOutAlt" />
        <span>Cerrar Sesión</span>
      </button>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  width: 220px;
  min-height: calc(100vh - 56px);
  background: rgba(6, 68, 54, 0.4);
  border-right: 1px solid rgba(212, 175, 55, 0.15);
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
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.15s;
  color: #94a3b8;
  font-size: 13px;
  white-space: nowrap;
}

.sidebar.collapsed .nav-item {
  justify-content: center;
  padding: 12px;
}

.nav-item:hover {
  background: rgba(212, 175, 55, 0.08);
  color: #e8c84a;
}

.nav-item.active {
  background: rgba(212, 175, 55, 0.12);
  color: #d4af37;
  border-left: 3px solid #d4af37;
}

.sidebar.collapsed .nav-item.active {
  border-left: none;
  border-bottom: 3px solid #d4af37;
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
  font-size: 12px;
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
  padding-left: 16px;
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
  background: rgba(255, 255, 255, 0.05);
  color: #94a3b8;
}

.submenu-item.active {
  background: rgba(212, 175, 55, 0.1);
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
  border-top: 1px solid rgba(212, 175, 55, 0.15);
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
  background: rgba(212, 175, 55, 0.15);
  border: 1px solid rgba(212, 175, 55, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  color: #d4af37;
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
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 10px;
  background: transparent;
  border: 1px solid rgba(212, 175, 55, 0.2);
  border-radius: 8px;
  color: #94a3b8;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.15s;
}

.logout-btn:hover {
  background: rgba(239, 68, 68, 0.15);
  border-color: rgba(239, 68, 68, 0.4);
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
