<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../../composables/useApi'

const props = defineProps({
  currentRoute: { type: String, default: 'dashboard' }
})

const emit = defineEmits(['navigate', 'newLoan', 'newCustomer', 'newPayment'])
const router = useRouter()
const api = useApi()

const user = ref({ name: 'Admin', email: '' })
const showPlusMenu = ref(false)

const menuItems = [
  { id: 'dashboard', label: ' inicio', icon: '◈' },
  { id: 'clientes', label: 'Clientes', icon: '⊕' },
  { id: 'prestamos', label: 'Cartera', icon: '◎' },
  { id: 'pagos', label: 'Pagos', icon: '◉' },
  { id: 'reportes', label: 'Reportes', icon: '▤' }
]

function logout() {
  localStorage.removeItem('token')
  router.push('/login')
}

function openPlusMenu() {
  showPlusMenu.value = !showPlusMenu.value
}

function closeMenu() {
  showPlusMenu.value = false
}

function handleNew(type) {
  emit('new' + type)
  closeMenu()
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
  <aside class="sidebar">
    <div class="logo-section">
      <img src="/logo.webp" alt="Logo" class="logo-img" />
    </div>
    <nav class="nav">
      <div 
        v-for="item in menuItems" 
        :key="item.id"
        class="nav-item"
        :class="{ active: currentRoute === item.id }"
        @click="emit('navigate', item.id)"
      >
        <span class="nav-icon">{{ item.icon }}</span>
        <span class="nav-label">{{ item.label }}</span>
      </div>
    </nav>
    <div class="sidebar-footer">
      <div class="user">
        <span class="user-avatar">JM</span>
        <div class="user-info">
          <div class="user-name">Admin</div>
          <div class="user-role">Administrador</div>
        </div>
      </div>
      <button class="logout-btn" @click="logout">Cerrar Sesión</button>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  width: 220px;
  min-height: 100vh;
  background: rgba(0,0,0,0.3);
  border-right: 1px solid rgba(255,255,255,0.07);
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
}

.logo-section {
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
}

.logo-img {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  padding: 8px;
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

.nav-icon {
  font-size: 14px;
}

.plus-section {
  padding: 12px;
  position: relative;
}

.plus-btn {
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #d4af37, #b89150);
  border: none;
  border-radius: 8px;
  color: #06090f;
  font-size: 20px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}

.plus-btn:hover {
  transform: scale(1.02);
  box-shadow: 0 4px 12px rgba(212,175,55,0.3);
}

.plus-menu {
  position: absolute;
  bottom: 100%;
  left: 12px;
  right: 12px;
  background: rgba(8,12,16,0.95);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 8px;
  margin-bottom: 8px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.plus-item {
  padding: 10px 12px;
  border-radius: 6px;
  cursor: pointer;
  color: #94a3b8;
  font-size: 13px;
  transition: all 0.15s;
}

.plus-item:hover {
  background: rgba(212,175,55,0.1);
  color: #d4af37;
}

.sidebar-footer {
  padding: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
}

.user {
  display: flex;
  align-items: center;
  gap: 10px;
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
  margin-top: 12px;
  padding: 8px;
  background: transparent;
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 6px;
  color: #94a3b8;
  font-size: 11px;
  cursor: pointer;
  transition: all 0.15s;
}

.logout-btn:hover {
  background: rgba(239,68,68,0.1);
  border-color: rgba(239,68,68,0.3);
  color: #ef4444;
}
</style>