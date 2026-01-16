# E-Commerce Platform

Modern e-commerce platform with multi-tenancy support, built with Laravel 11 (Backend) and Vue 3 (Frontend).

## 📋 Table of Contents

- [Overview](#overview)
- [Technology Stack](#technology-stack)
- [Folder Structure](#folder-structure)
- [Application Flow](#application-flow)
- [Installation](#installation)
- [User Guide](#user-guide)
- [API Endpoints](#api-endpoints)
- [Features](#features)

## 🎯 Overview

Full-stack e-commerce application with features:
- **Multi-Tenancy**: Support for multiple tenants/shops in a single application
- **Authentication**: Login/register system with JWT tokens (Laravel Sanctum)
- **Role-Based Access Control**: Admin and Customer roles
- **Shopping Cart**: Shopping cart with item management
- **Product Management**: Admins can manage products with CRUD operations
- **Responsive Design**: Mobile-friendly interface with Tailwind CSS

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 11
- **Database**: SQLite (development)
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **Multi-Tenancy**: Stancl/Tenancy
- **PHP Version**: 8.2+

### Frontend
- **Framework**: Vue 3 (Composition API)
- **Build Tool**: Vite
- **CSS Framework**: Tailwind CSS v4
- **State Management**: Pinia
- **Routing**: Vue Router
- **HTTP Client**: Axios

## 📁 Folder Structure

```
task-mr-ahmad/
├── backend/                          # Aplikasi Laravel
│   ├── app/
│   │   ├── Application/              # Use Case Layer
│   │   │   ├── Controllers/          # Request handlers
│   │   │   ├── Requests/             # Form validation
│   │   │   └── Resources/            # Response formatting
│   │   ├── Domain/                   # Domain Layer (DDD)
│   │   │   ├── Cart/                 # Cart domain logic
│   │   │   │   ├── Models/
│   │   │   │   ├── Services/
│   │   │   │   └── Repositories/
│   │   │   ├── Product/              # Product domain logic
│   │   │   │   ├── Models/
│   │   │   │   ├── Services/
│   │   │   │   └── Repositories/
│   │   │   ├── User/                 # User domain logic
│   │   │   └── Tenant/               # Tenant domain logic
│   │   ├── Infrastructure/           # Infrastructure Layer
│   │   │   ├── Persistence/          # Repository implementations
│   │   │   └── Cache/                # Caching logic
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   ├── Models/                   # Eloquent models
│   │   └── Providers/                # Service providers
│   ├── bootstrap/
│   │   └── app.php                   # Application configuration
│   ├── config/                       # Configuration files
│   ├── database/
│   │   ├── migrations/               # Database migrations
│   │   ├── seeders/                  # Database seeders
│   │   └── factories/                # Model factories
│   ├── routes/
│   │   ├── web.php                   # Web routes (API)
│   │   └── tenant.php                # Tenant-specific routes
│   ├── public/                       # Public assets
│   └── storage/                      # Logs, cache, uploads
│
├── frontend/                         # Aplikasi Vue 3
│   ├── src/
│   │   ├── api/                      # API request functions
│   │   ├── components/               # Reusable components
│   │   ├── layouts/                  # Layout components
│   │   ├── pages/                    # Page components
│   │   │   ├── HomePage.vue
│   │   │   ├── ProductsPage.vue
│   │   │   ├── ProductDetailPage.vue
│   │   │   ├── CartPage.vue
│   │   │   ├── CheckoutPage.vue
│   │   │   ├── LoginPage.vue
│   │   │   ├── RegisterPage.vue
│   │   │   └── admin/                # Admin pages
│   │   │       ├── AdminDashboard.vue
│   │   │       ├── ProductsManagement.vue
│   │   │       └── TenantsManagement.vue
│   │   ├── router/                   # Vue Router configuration
│   │   ├── stores/                   # Pinia stores
│   │   │   ├── auth.js               # Authentication state
│   │   │   ├── cart.js               # Cart state
│   │   │   └── products.js           # Products state
│   │   ├── utils/                    # Utility functions
│   │   ├── App.vue                   # Root component
│   │   ├── main.js                   # Entry point
│   │   └── style.css                 # Global styles
│   ├── index.html                    # HTML template
│   ├── vite.config.js                # Vite configuration
│   └── tailwind.config.js            # Tailwind configuration
│
└── README.md                         # This file
```

## 🔄 Application Flow

### Authentication Flow
```
User Registration
    ↓
POST /api/auth/register
    ↓
Backend: Validation → Create User → Return Token + User Data
    ↓
Frontend: Save Token (localStorage) → Navigate to Home
    ↓
Automatic Token Attachment: Axios interceptor adds token to every request
```

### Login Flow
```
User Login Form
    ↓
POST /api/auth/login
    ↓
Backend: Verify Credentials → Return Token + User + Roles
    ↓
Frontend: Auth Store → Check Admin Status → Route Guard
    ↓
Access Granted/Redirect based on role
```

### Product Viewing Flow
```
Homepage / Products Page
    ↓
GET /api/products (public)
    ↓
Backend: Fetch Active Products → Return JSON
    ↓
Frontend: Store in Pinia → Display Grid
    ↓
User Click Product
    ↓
GET /api/products/{id} (public)
    ↓
Show Details + Add to Cart Option
```

### Shopping Cart Flow
```
User Click "Add to Cart"
    ↓
POST /api/cart/items
    ↓
Backend: 
  - Find/Create Cart (by session or user)
  - Check Product availability
  - Calculate subtotal
  - Return Updated Cart
    ↓
Frontend: Cart Store → Update UI
    ↓
User Review Cart → Click "Proceed to Checkout"
    ↓
GET /checkout
    ↓
Show Checkout Form (Shipping + Payment Method)
    ↓
POST /checkout (submit order)
    ↓
Clear Cart → Success Message
```

### Admin Product Management Flow
```
Admin Login
    ↓
Check Role = "admin" → Access Admin Dashboard
    ↓
Navigate to Products Management
    ↓
GET /api/products (protected, admin only)
    ↓
Display Product List with Actions:
  - Create: POST /api/products
  - Edit: PUT /api/products/{id}
  - Delete: DELETE /api/products/{id}
    ↓
Update Reflected in Store
```

### Database Flow (Entity Relationships)
```
users
  ├── id (primary)
  ├── name, email, password
  ├── phone, address, city, state, country, postal_code
  ├── is_active
  └── roles (pivot: model_has_roles)

products
  ├── id (primary)
  ├── name, slug, description
  ├── price, cost_per_item, compare_at_price
  ├── stock, sku, barcode
  ├── is_active, is_featured
  ├── image, images (JSON)
  └── timestamps

carts
  ├── id (primary)
  ├── user_id (foreign → users)
  ├── session_id
  ├── status: active|completed|abandoned
  ├── subtotal, tax, shipping, total
  └── cart_items[]

cart_items
  ├── id (primary)
  ├── cart_id (foreign → carts)
  ├── product_id (foreign → products)
  ├── quantity, price, subtotal
  └── product_snapshot (JSON)

roles
  ├── admin
  └── customer

permissions (related to roles)
```

## 🚀 Installation

### Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate:fresh --seed

# Start development server
php artisan serve
```

**Backend runs on**: `http://localhost:8000`

### Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev
```

**Frontend runs on**: `http://localhost:5174`

## 📖 User Guide

### Test Accounts

**Admin Account**
- Email: `admin@example.com`
- Password: `admin123`

**Customer Accounts**
- Email: `john@example.com`, Password: `password123`
- Email: `jane@example.com`, Password: `password123`
- Email: `bob@example.com`, Password: `password123`

### Customer Flow

1. **Browse Products**
   - Visit homepage or `/products`
   - See list of active products
   - Click product to view details

2. **Add to Cart**
   - On product detail page, select quantity
   - Click "Add to Cart"
   - Item added to cart

3. **View Cart**
   - Click shopping cart icon
   - See all items with subtotal
   - Update quantity or remove items

4. **Checkout**
   - Click "Proceed to Checkout"
   - Fill shipping information
   - Select payment method
   - Click "Place Order"
   - Order complete, cart cleared

### Admin Flow

1. **Login as Admin**
   - Use admin credentials
   - Dashboard automatically accessible

2. **Manage Products**
   - Access Admin → Products Management
   - View all products with actions
   - Create: Fill form, click Create
   - Edit: Click Edit, change data, Save
   - Delete: Click Delete, confirm

3. **View Analytics** (future feature)
   - Dashboard shows sales summary
   - Total orders, revenue, top products

## 📡 API Endpoints

### Authentication
```
POST   /api/auth/register      - User registration
POST   /api/auth/login         - User login
POST   /api/auth/logout        - User logout (authenticated)
GET    /api/auth/me            - Get current user (authenticated)
```

### Products (Public)
```
GET    /api/products           - List all active products
GET    /api/products/featured  - List featured products
GET    /api/products/{id}      - Get product detail
```

### Products (Admin Only)
```
POST   /api/products           - Create product (authenticated, admin)
PUT    /api/products/{id}      - Update product (authenticated, admin)
DELETE /api/products/{id}      - Delete product (authenticated, admin)
```

### Cart (Public/Authenticated)
```
GET    /api/cart               - Get current cart
POST   /api/cart/items         - Add item to cart
PUT    /api/cart/items/{id}    - Update cart item quantity
DELETE /api/cart/items/{id}    - Remove item from cart
DELETE /api/cart/clear         - Clear all cart items
POST   /api/cart/merge         - Merge guest cart to user cart (authenticated)
```

## ✨ Features

### ✅ Implemented
- [x] User Registration & Login
- [x] Product Browsing (public)
- [x] Shopping Cart Management
- [x] Checkout Process
- [x] Admin Login
- [x] Admin Product CRUD
- [x] Role-Based Access Control
- [x] JWT Authentication
- [x] Responsive Design
- [x] Form Validation
- [x] Error Handling
- [x] Database Seeding

### 🔄 Future Features
- [ ] Payment Gateway Integration
- [ ] Order Management System
- [ ] User Order History
- [ ] Product Reviews & Ratings
- [ ] Wishlist Feature
- [ ] Advanced Search & Filters
- [ ] Email Notifications
- [ ] Admin Dashboard Analytics
- [ ] Inventory Management
- [ ] Multi-currency Support
- [ ] Coupon/Discount System

## 📝 Important Notes

### CORS Configuration
Frontend on port 5174 is allowed to access backend on port 8000.

### Database
Uses SQLite for development. For production, use MySQL/PostgreSQL.

### Environment Variables
Configure in `backend/.env`:
```
APP_URL=http://localhost:8000
APP_DEBUG=true
SANCTUM_STATEFUL_DOMAINS=localhost:5174
SESSION_DOMAIN=localhost
```

Frontend in `frontend/.env` (if needed):
```
VITE_API_URL=http://localhost:8000
```

### Session & Auth
- Frontend: Token stored in localStorage
- Backend: Uses Laravel Sanctum for token-based auth
- Cart: Uses session ID for guests or user ID for authenticated users

## 🤝 Contributing

For development:
1. Create feature branch
2. Commit changes
3. Test before push
4. Create pull request

## 📞 Support

For questions or issues, please contact the development team.

---

**Last Updated**: January 16, 2026
