import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import SideBar from './components/SideBar.vue'
import Info from './components/Info.vue'

createApp(App)
  .use(router)
  .component('SideBar', SideBar)
  .component('Info', Info)
  .mount('#app')
