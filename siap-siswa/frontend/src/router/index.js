import Vue from 'vue'
import VueRouter from 'vue-router'
import LoginView from '../views/LoginView.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Login',
    component: LoginView
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: () => import('../views/AdminDashboard.vue'),
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/guru',
    name: 'GuruDashboard',
    component: () => import('../views/GuruDashboard.vue'),
    meta: { requiresAuth: true, role: 'guru' }
  },
  {
    path: '/orangtua',
    name: 'OrangTuaDashboard',
    component: () => import('../views/OrangTuaDashboard.vue'),
    meta: { requiresAuth: true, role: 'orangtua' }
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

// Navigation guard for role-based access
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('user')
  const userRole = isAuthenticated ? JSON.parse(isAuthenticated).role : null

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/')
  } else if (to.meta.role && to.meta.role !== userRole) {
    next('/')
  } else {
    next()
  }
})

export default router
