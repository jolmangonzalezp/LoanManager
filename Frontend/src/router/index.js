import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  { path: '/', name: 'dashboard', component: () => import('@/Modules/Dashboard/View/DashboardView.vue') },
  { path: '/login', name: 'login', component: () => import('@/Modules/Auth/View/Login.vue') },
  { path: '/clientes', name: 'clientes', component: () => import('@/Modules/Customer/View/CustomersView.vue') },
  { path: '/prestamos', name: 'prestamos', component: () => import('@/Modules/Loan/View/LoansView.vue') },
  { path: '/pagos', name: 'pagos', component: () => import('@/Modules/Payment/View/PaymentsView.vue') },
  { path: '/reportes', name: 'reportes', component: () => import('@/Modules/Report/View/ReportsView.vue') },
  { path: '/reportes/cartera', name: 'reportes-cartera', component: () => import('@/Modules/Report/View/reports/PortfolioReport.vue') },
  { path: '/reportes/rentabilidad', name: 'reportes-rentabilidad', component: () => import('@/Modules/Report/View/reports/ProfitabilityReport.vue') },
  { path: '/reportes/mora', name: 'reportes-mora', component: () => import('@/Modules/Report/View/reports/DelinquencyReport.vue') },
  { path: '/reportes/prestamos-activos', name: 'reportes-prestamos-activos', component: () => import('@/Modules/Report/View/reports/ActiveLoansReport.vue') },
  { path: '/reportes/flujo-caja', name: 'reportes-flujo-caja', component: () => import('@/Modules/Report/View/reports/CashFlowReport.vue') },
  { path: '/reportes/historial-pagos', name: 'reportes-historial-pagos', component: () => import('@/Modules/Report/View/reports/PaymentHistoryReport.vue') },
  { path: '/reportes/auditoria', name: 'reportes-auditoria', component: () => import('@/Modules/Report/View/reports/AuditReport.vue') },
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
