import { createRouter, createWebHistory } from 'vue-router'
import { AuthPage } from '@/Modules/Auth';
import { DashboardPage } from '@/Modules/Dashboard';
import { CustomerPage } from '@/Modules/Customer';
import { LoanPage } from '@/Modules/Loan';
import { PaymentPage } from '@/Modules/Payment';
import { UserPage } from '@/Modules/User';
import { RoutePage } from '@/Modules/Route';
import { CashFlow, DelinquencyReport, PortfolioReport, ProfitbilityReport, SummaryReport } from '@/Modules/Report';
import ActiveLoansReport from '@/Modules/Report/View/reports/ActiveLoansReport.vue';
import PaymentHistoryReport from '@/Modules/Report/View/reports/PaymentHistoryReport.vue';


const routes = [
    {path: '/', name: 'Dashboard', component: DashboardPage},
    {path: '/login', name: 'Login', component: AuthPage},
    { path: '/clientes', name: 'clientes', component: CustomerPage },
    { path: '/prestamos', name: 'prestamos', component: LoanPage },
    { path: '/pagos', name: 'pagos', component: PaymentPage },
    { path: '/usuarios', name: 'usuarios', component: UserPage },
    { path: '/rutas', name: 'rutas', component: RoutePage },
    { path: '/reportes', name: 'reportes', component: SummaryReport },
    { path: '/reportes/cartera', name: 'reportes-cartera', component: PortfolioReport },
    { path: '/reportes/rentabilidad', name: 'reportes-rentabilidad', component: ProfitbilityReport },
    { path: '/reportes/mora', name: 'reportes-mora', component: DelinquencyReport },
    { path: '/reportes/prestamos-activos', name: 'reportes-prestamos-activos', component: ActiveLoansReport },
    { path: '/reportes/flujo-caja', name: 'reportes-flujo-caja', component: CashFlow },
    { path: '/reportes/historial-pagos', name: 'reportes-historial-pagos', component: PaymentHistoryReport },
    // { path: '/reportes/auditoria', name: 'reportes-auditoria', component: () => import('@/Modules/Report/View/reports/AuditReport.vue') },
    { path: '/geo-test', name: 'geo-test', component: () => import('@/Modules/Geo/View/GeoTestView.vue') },
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
