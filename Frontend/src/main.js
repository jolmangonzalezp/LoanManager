import { createApp } from 'vue'
import './assets/tokens.css'
import App from './App.vue'
import router from './router'
import { useApi } from './Shared/Composable/useApi'
import Swal from 'vue-sweetalert2'

const app = createApp(App)
app.use(router)
app.use(Swal)
app.mount('#app')

useApi().initToken()