import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  { path: '/', name: 'dashboard', component: () => import('@/Modules/Dashboard/View/DashboardView.vue') },
  { path: '/login', name: 'login', component: () => import('@/Modules/Auth/View/Login.vue') },
  { path: '/clientes', name: 'clientes', component: () => import('@/Modules/Customer/View/CustomersView.vue') },
  { path: '/prestamos', name: 'prestamos', component: () => import('@/Modules/Loan/View/LoansView.vue') },
  { path: '/pagos', name: 'pagos', component: () => import('@/Modules/Payment/View/PaymentsView.vue') },
  { path: '/reportes', name: 'reportes', component: () => import('@/Modules/Payment/View/ReportsView.vue') }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  if (to.path !== '/login' && !token) next('/login')
  else if (to.path === '/login' && token) next('/')
  else next()
})

export default router