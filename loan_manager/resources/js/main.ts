import { createApp } from 'vue';
import App from './App.vue';
import Swal from 'vue-sweetalert2'
import router from '@/routes';
import '@/Shared/Assets/tokens.css';

const app = createApp(App)
app.use(router)
app.use(Swal)
app.mount('#app')
