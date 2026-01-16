# Frontend - Vue 3 E-Commerce Platform

E-commerce frontend application using **Vue 3** (Composition API), **Vite**, **Tailwind CSS v4**, **Pinia** (state management), and **Vue Router**.

## 📚 Table of Contents

- [🏗️ Architecture](#-architecture)
- [📁 Project Structure](#-project-structure)
- [⚙️ Setup & Installation](#-setup--installation)
- [🔧 Configuration](#-configuration)
- [📦 State Management (Pinia)](#-state-management-pinia)
- [🛣️ Routing](#-routing)
- [🔐 Authentication](#-authentication)
- [📡 API Integration](#-api-integration)
- [🧪 Development](#-development)
- [📚 Component Documentation](#-component-documentation)
- [🐛 Troubleshooting](#-troubleshooting)

---

## 🏗️ Architecture

### Component Architecture

Vue 3 application follows a modular structure with clear separation of concerns:

```
Pages (Routes)
    ↓
Layouts (Wrappers)
    ↓
Components (Reusable UI)
    ↓
Stores (Pinia - State Management)
    ↓
API Integration (Axios)
    ↓
Backend API
```

### Technology Stack

| Tech | Purpose |
|------|---------|
| **Vue 3** | Progressive JavaScript framework |
| **Composition API** | Modern Vue API for component logic |
| **Vite** | Next-gen frontend build tool |
| **Tailwind CSS v4** | Utility-first CSS framework |
| **Pinia** | State management (Vuex successor) |


---

## ⚙️ Setup & Instalasi

### Prerequisites

- **Node.js** 16+ dan **NPM** 7+
- Text editor (VS Code recommended)
- Backend API running on `http://localhost:8000`

### Installation Steps

#### 1. Navigate to Frontend Directory
```bash
cd /path/to/task-mr-ahmad/frontend
```

#### 2. Install Dependencies
```bash
npm install
```

#### 3. Environment Configuration

Create `.env.local` file (if needed):
```env
VITE_API_BASE_URL=http://localhost:8000/api
```

#### 4. Start Development Server
```bash
npm run dev
# Server berjalan di http://localhost:5173
# Atau fallback ke http://localhost:5174
```

#### 5. Build for Production
```bash
npm run build
# Output: dist/ folder
```

#### 6. Preview Production Build
```bash
npm run preview
```

---

## 🔧 Konfigurasi

### Vite Configuration (`vite.config.js`)

```javascript
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    port: 5173,
    open: true
  }
})
```

### Tailwind CSS Configuration (`tailwind.config.js`)

```javascript
module.exports = {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}'
  ],
  theme: {
    extend: {}
  },
  plugins: []
}
```

### API Client Configuration (`src/api/client.js`)

```javascript
import axios from 'axios'

const client = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
})

// Add token to every request
client.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default client
```

---

## 📦 State Management (Pinia)

### Auth Store (`stores/auth.js`)

Manage authentication state dan user data:

```javascript
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))
  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.roles?.includes('admin'))

  async function login(email, password) {
    const response = await client.post('/auth/login', { email, password })
    token.value = response.data.token
    user.value = response.data.user
    localStorage.setItem('token', token.value)
  }

  async function logout() {
    await client.post('/auth/logout')
    token.value = null
    user.value = null
    localStorage.removeItem('token')
  }

  return { user, token, isAuthenticated, isAdmin, login, logout }
})
```

### Cart Store (`stores/cart.js`)

Manage shopping cart state:

```javascript
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import client from '@/api/client'

export const useCartStore = defineStore('cart', () => {
  const cart = ref(null)
  const items = computed(() => cart.value?.items || [])
  const total = computed(() => cart.value?.total || 0)

  async function fetchCart() {
    const response = await client.get('/cart')
    cart.value = response.data.data
  }

  async function addItem(productId, quantity) {
    await client.post('/cart/items', { product_id: productId, quantity })
    await fetchCart()
  }

  async function removeItem(itemId) {
    await client.delete(`/cart/items/${itemId}`)
    await fetchCart()
  }

  return { cart, items, total, fetchCart, addItem, removeItem }
})
```

### Products Store (`stores/products.js`)

Manage products state:

```javascript
import { defineStore } from 'pinia'
import { ref } from 'vue'
import client from '@/api/client'

export const useProductsStore = defineStore('products', () => {
  const products = ref([])
  const selectedProduct = ref(null)

  async function fetchProducts(filters = {}) {
    const response = await client.get('/products', { params: filters })
    products.value = response.data.data
  }

  async function fetchProduct(id) {
    const response = await client.get(`/products/${id}`)
    selectedProduct.value = response.data.data
  }

  return { products, selectedProduct, fetchProducts, fetchProduct }
})
```

---

## 🛣️ Routing

### Router Configuration (`src/router/index.js`)

```javascript
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    component: () => import('../layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'Home',
        component: () => import('../pages/HomePage.vue')
      },
      {
        path: '/products',
        name: 'Products',
        component: () => import('../pages/ProductsPage.vue')
      },
      {
        path: '/products/:id',
        name: 'ProductDetail',
        component: () => import('../pages/ProductDetailPage.vue')
      },
      {
        path: '/cart',
        name: 'Cart',
        component: () => import('../pages/CartPage.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/checkout',
        name: 'Checkout',
        component: () => import('../pages/CheckoutPage.vue'),
        meta: { requiresAuth: false }
      }
    ]
  },
  {
    path: '/auth',
    component: () => import('../layouts/AuthLayout.vue'),
    children: [
      {
        path: 'login',
        name: 'Login',
        component: () => import('../pages/LoginPage.vue')
      },
      {
        path: 'register',
        name: 'Register',
        component: () => import('../pages/RegisterPage.vue')
      }
    ]
  },
  {
    path: '/admin',
    component: () => import('../layouts/MainLayout.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: () => import('../pages/AdminDashboard.vue')
      },
      {
        path: 'products',
        name: 'ProductsManagement',
        component: () => import('../pages/ProductsManagement.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Route guard untuk authentication
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next({ name: 'Login' })
  } else if (to.meta.requiresAdmin && !auth.isAdmin) {
    next({ name: 'Home' })
  } else {
    next()
  }
})

export default router
```

---

## 🔐 Authentication

### Login Flow

```
User navigates to /auth/login
    ↓
LoginPage.vue form
    ↓
Submit credentials
    ↓
API: POST /api/auth/login
    ↓
Receive token + user data
    ↓
Store in localStorage + Pinia
    ↓
Redirect to /products
```

### Register Flow

```
User navigates to /auth/register
    ↓
RegisterPage.vue form
    ↓
Validate input (password match, email format)
    ↓
Submit to API
    ↓
API: POST /api/auth/register
    ↓
Auto-login user (receive token)
    ↓
Redirect to /products
```

### Protected Routes

Routes marked dengan `meta: { requiresAuth: true }` akan redirect ke login jika user tidak authenticated.

---

## 📡 API Integration

### API Client Setup

All API calls go through `src/api/client.js`:

```javascript
import client from '@/api/client'

// GET request
const response = await client.get('/products')

// POST request
const response = await client.post('/cart/items', {
  product_id: 1,
  quantity: 2
})

// PUT request
const response = await client.put('/cart/items/1', {
  quantity: 3
})

// DELETE request
await client.delete('/cart/items/1')
```

### API Error Handling

```javascript
try {
  await client.post('/auth/login', { email, password })
} catch (error) {
  if (error.response?.status === 401) {
    // Invalid credentials
    showError('Email atau password salah')
  } else if (error.response?.status === 422) {
    // Validation errors
    showErrors(error.response.data.errors)
  } else {
    // Network or server error
    showError('Terjadi kesalahan')
  }
}
```

---

## 🧪 Development

### Development Server

```bash
npm run dev
# Starts with HMR (Hot Module Replacement)
# Auto-reload on file changes
```

### Building

```bash
npm run build
# Optimized production build
# Output: dist/ folder
```

### Browser DevTools

**Vue DevTools** extension recommended:
- [Chrome](https://chrome.google.com/webstore/detail/vuejs-devtools)
- [Firefox](https://addons.mozilla.org/en-US/firefox/addon/vue-js-devtools/)

**Using DevTools:**
- Inspect Vue components
- View Pinia store state
- Track component lifecycle

---

## 📚 Component Documentation

### Navbar Component (`components/common/Navbar.vue`)

**Purpose:** Navigation bar dengan links dan user menu

**Props:** None

**Usage:**
```vue
<Navbar />
```

**Features:**
- Links: Home, Products, Cart, Admin (jika admin)
- User menu: Profile, Logout
- Mobile responsive

### ProductCard Component (`components/products/ProductCard.vue`)

**Purpose:** Display single product dalam grid

**Props:**
```javascript
{
  product: {
    id: Number,
    name: String,
    slug: String,
    price: Number,
    image: String,
    is_active: Boolean
  }
}
```

**Usage:**
```vue
<ProductCard :product="product" />
```

### CartSummary Component (`components/cart/CartSummary.vue`)

**Purpose:** Show cart totals (subtotal, tax, shipping, total)

**Props:**
```javascript
{
  cart: Object  // Cart object from store
}
```

**Usage:**
```vue
<CartSummary :cart="cart" />
```

---

## 🐛 Troubleshooting

### Common Issues

**1. "Cannot GET /products" (404)**
- Check if backend API is running on `http://localhost:8000`
- Verify `VITE_API_BASE_URL` in API client

**2. "CORS Error: Access-Control-Allow-Origin"**
- Backend CORS not configured
- Check `bootstrap/app.php` in backend
- Restart backend server

**3. "Token invalid or expired"**
- Token stored in localStorage is no longer valid
- Clear localStorage: `localStorage.clear()`
- Re-login user
- Check token expiration in backend config

**4. "Pinia store not working"**
- Verify store is properly imported
- Use `useStoreName()` hook in components
- Check store name matches import

**5. "Vite port 5173 already in use"**
- Kill process: `lsof -i :5173 | grep -v PID | awk '{print $2}' | xargs kill -9`
- Or use different port: `npm run dev -- --port 5174`

### Debug Tips

**Vue DevTools:**
- Open browser DevTools
- Go to Vue tab
- Inspect components and props

**Pinia Inspector:**
- Open DevTools
- Go to Pinia tab
- Track state changes

**Network Tab:**
- Monitor API requests/responses
- Check headers (Authorization, Content-Type)
- Verify request/response payloads

**Console:**
```javascript
// Check auth store
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
console.log(auth.user)
console.log(auth.token)

// Check products
import { useProductsStore } from '@/stores/products'
const products = useProductsStore()
console.log(products.products)
```

---

## 🚀 Deployment

### Build for Production

```bash
npm run build
# Creates optimized dist/ folder
```

### Deploy to Server

```bash
# Copy dist folder to web server
scp -r dist/* user@server:/var/www/html/

# Or use deployment tools (Vercel, Netlify, etc.)
```

### Environment Variables for Production

Create `.env.production.local`:
```env
VITE_API_BASE_URL=https://api.example.com/api
```

---

## 📚 Resources

- [Vue 3 Documentation](https://vuejs.org)
- [Vite Documentation](https://vitejs.dev)
- [Tailwind CSS](https://tailwindcss.com)
- [Pinia Documentation](https://pinia.vuejs.org)
- [Vue Router](https://router.vuejs.org)

---

**Last Updated:** January 2026
**Maintained by:** Development Team
- Safari (latest)
- Edge (latest)

## Development Tips

### Hot Module Replacement (HMR)

Vite provides instant HMR. Changes to Vue components will reflect immediately without full page reload.

### Vue DevTools

Install [Vue DevTools](https://devtools.vuejs.org/) browser extension for debugging:
- Inspect component hierarchy
- View Pinia store state
- Track component events
- Time-travel debugging

### Debugging

```javascript
// Enable detailed error messages in development
if (import.meta.env.DEV) {
  console.log('Development mode')
}
```

## Troubleshooting

### Common Issues

1. **API connection errors**
   - Verify `VITE_API_URL` in `.env`
   - Ensure backend server is running
   - Check CORS settings in backend

2. **Build errors**
   ```bash
   # Clear node_modules and reinstall
   rm -rf node_modules package-lock.json
   npm install
   ```

3. **Styling not working**
   ```bash
   # Rebuild Tailwind
   npm run dev
   ```

## Contributing

1. Follow Vue 3 composition API best practices
2. Use `<script setup>` syntax for components
3. Keep components focused and reusable
4. Write meaningful commit messages

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
