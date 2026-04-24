<script setup>
import { computed, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import Sidebar from '@/Shared/Components/Sidebar.vue'
import ModalHost from '@/Shared/Components/ModalHost.vue'
import { useModal } from '@/Shared/Composable/useModal'
import { PaymentFormModal } from '@/Modules/Payment'
import { useCustomerApi } from '@/Modules/Customer'
import { useLoanApi } from '@/Modules/Loan'
import { useAlert } from '@/Shared/Composable/useAlert'

const router = useRouter()
const route = useRoute()
const modal = useModal()
const fabOpen = ref(false)
const sidebarCollapsed = ref(false)

const showSidebar = computed(() => route.path !== '/login')

const currentRoute = computed(() => {
  const path = route.path
  if (path === '/') return 'dashboard'
  if (path.startsWith('/clientes')) return 'clientes'
  if (path.startsWith('/prestamos')) return 'prestamos'
  if (path.startsWith('/pagos')) return 'pagos'
  if (path.startsWith('/reportes')) return 'reportes'
  return 'dashboard'
})

const navigate = (to) => router.push('/' + to)

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

</script>

<template>
  <div class="app">
    <Sidebar v-if="showSidebar" 
      :current-route="currentRoute" 
      :collapsed="sidebarCollapsed"
      @navigate="navigate"
      @toggle="toggleSidebar"
    />

    <header v-if="showSidebar" class="header">
      <button class="menu-toggle" @click="toggleSidebar">
        <span class="toggle-icon">{{ sidebarCollapsed ? '☰' : '✕' }}</span>
      </button>
      <div class="header-logo">
        <img src="/logo.webp" alt="Logo" class="logo-img" />
      </div>
      <div class="header-spacer"></div>
    </header>

    <main class="main" :class="{ 'no-sidebar': !showSidebar }">
      <router-view />
    </main>

    <ModalHost />
  </div>
</template>

<style scoped>
.app { display: flex; min-height: 100vh; }
.main { flex: 1; padding: 32px 30px 80px; min-height: calc(100vh - 56px); margin-top: 56px; margin-left: 220px; }
.main.no-sidebar { margin-left: 0; padding: 0; }

.header {
  height: 56px;
  display: flex;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 250;
}

.menu-toggle {
  position: fixed;
  left: 16px;
  width: 40px;
  height: 40px;
  border: none;
  background: transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 260;
}

.toggle-icon {
  font-size: 20px;
  color: #d4af37;
}

.header-logo {
  position: fixed;
  left: 50%;
  transform: translateX(-50%);
  height: 100%;
  display: flex;
  align-items: center;
  padding: 6px;
}

.logo-img {
  height: 44px;
  width: auto;
  border-radius: 50%;
}

.header-spacer {
  width: 40px;
}

.fab {
  position: fixed;
  bottom: 32px;
  right: 32px;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #d4af37, #b89150);
  border: none;
  color: #06090f;
  font-size: 28px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 18px rgba(212,175,55,0.25);
  z-index: 100;
}

.fab:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 24px rgba(212,175,55,0.35);
}

.fab-menu {
  position: absolute;
  bottom: 100%;
  right: 0;
  margin-bottom: 12px;
  background: rgba(8,12,16,0.95);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 10px;
  padding: 8px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 160px;
}

.fab-item {
  padding: 12px 16px;
  border-radius: 8px;
  cursor: pointer;
  color: #94a3b8;
  font-size: 13px;
  transition: all 0.15s;
}

.fab-item:hover {
  background: rgba(212,175,55,0.1);
  color: #d4af37;
}
</style>
