import {computed, ref, watch} from "vue";
import {
    faChartBar, faChartLine,
    faChartPie, faExclamationTriangle, faFileAlt, faFileInvoiceDollar,
    faHandHoldingDollar, faHistory,
    faHome,
    faMoneyBillTransfer,
    faRoute,
    faUsers
} from "@fortawesome/free-solid-svg-icons";
import {useRoute} from "vue-router";

const isMenuOpened = ref<boolean>(false);

export  const useLayout = () => {

    const route = useRoute();
    const isReport = computed(() => route?.path?.startsWith('/reportes'));
    const login = computed(() => route?.path === '/login');

    const routes = [
        { id:1, label:'Dashboard', icon:faHome, to:'/' },
        { id: 2, label:'Clientes', icon:faUsers, to:'/clientes' },
        { id: 3, label:'Préstamo', icon:faHandHoldingDollar, to:'/prestamos' },
        { id: 4, label:'Pagos', icon:faMoneyBillTransfer, to:'/pagos' },
        { id: 5, label:'Rutas', icon:faRoute, to:'/rutas' },
        { id: 6, label:'Reportes', icon:faChartPie, to:'/reportes' },
    ];

    const subroutes = [
        { id:51, label: 'Resumen', icon: faChartBar, to: '/reportes' },
        { id:52, label: 'Cartera', icon: faChartPie, to: '/reportes/cartera' },
        { id:53, label: 'Rentabilidad', icon: faChartLine, to: '/reportes/rentabilidad' },
        { id:54, label: 'Mora', icon: faExclamationTriangle, to: '/reportes/mora' },
        // { id:55, label: 'Préstamos Activos', icon: faFileAlt, to: '/reportes/prestamos-activos' },
        { id:56, label: 'Flujo de Caja', icon: faFileInvoiceDollar, to: '/reportes/flujo-caja' },
        // { id:57, label: 'Historial de Pagos', icon: faHistory, to: '/reportes/historial-pagos' },
        // { id:58, label: 'Auditoría', icon: faClipboardList, to: '/reportes/auditoria' },
    ]

    const toggleIconMenu = () => {
        isMenuOpened.value = !isMenuOpened.value;
    }

    const closeDrawer = () => {
        isMenuOpened.value = false;
    }

    const layoutHandler = computed(() => {
        if (login.value) return '';
        const size = isMenuOpened.value ? 'wide' : 'slim';
        const drawer = isReport.value ? '-drawer' : '';
        return `layout-${size}${drawer}`;
    });

    watch(() => route.path, () => {
        closeDrawer();
    });

    return {
        routes,
        subroutes,
        isMenuOpened,
        layoutHandler,
        toggleIconMenu,
        closeDrawer,
        login,
    }
}
