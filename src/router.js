import { createMemoryHistory, createRouter } from 'vue-router'
import SMSSenderView from './views/SMSSenderView.vue'
import SMSBlastView from './views/SMSBlastView.vue'
import TemplatingView from './views/Tools/TemplatingView.vue'

const routes = [
  { path: '/', component: SMSSenderView },
  { path: '/sms/sender', component: SMSSenderView },
  { path: '/sms/blast', component: SMSBlastView },
  { path: '/tools/templating', component: TemplatingView }
]

const router = createRouter({
  history: createMemoryHistory(),
  routes,
})

export default router