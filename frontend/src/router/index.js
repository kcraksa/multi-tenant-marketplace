import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    component: () => import('../layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'Home',
        component: () => import('../pages/HomePage.vue'),
      },
      {
        path: 'products',
        name: 'Products',
        component: () => import('../pages/ProductsPage.vue'),
      },
      {
        path: 'products/:id',
        name: 'ProductDetail',
        component: () => import('../pages/ProductDetailPage.vue'),
      },
      {
        path: 'cart',
        name: 'Cart',
        component: () => import('../pages/CartPage.vue'),
      },
      {
        path: 'checkout',
        name: 'Checkout',
        component: () => import('../pages/CheckoutPage.vue'),
        meta: { requiresAuth: false },
      },
      {
        path: 'login',
        name: 'Login',
        component: () => import('../pages/LoginPage.vue'),
        meta: { guest: true },
      },
      {
        path: 'register',
        name: 'Register',
        component: () => import('../pages/RegisterPage.vue'),
        meta: { guest: true },
      },
      {
        path: 'admin',
        name: 'Admin',
        component: () => import('../pages/admin/AdminDashboard.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
        children: [
          {
            path: 'products',
            name: 'AdminProducts',
            component: () => import('../pages/admin/ProductsManagement.vue'),
          },
          {
            path: 'tenants',
            name: 'AdminTenants',
            component: () => import('../pages/admin/TenantsManagement.vue'),
          },
        ],
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' })
  } else if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next({ name: 'Home' })
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'Home' })
  } else {
    next()
  }
})

export default router
