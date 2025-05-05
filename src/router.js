import { createMemoryHistory, createRouter } from 'vue-router'
import SMSSenderView from './views/SMSSenderView.vue'
import SMSBlastView from './views/SMSBlastView.vue'

const routes = [
  { path: '/', component: SMSBlastView },
  { path: '/sms/sender', component: SMSSenderView },
  { path: '/sms/blast', component: SMSBlastView },
]

const router = createRouter({
  history: createMemoryHistory(),
  routes,
})

export default router