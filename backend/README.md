# Backend - Laravel E-Commerce API

E-commerce backend application using **Laravel 11** with **Domain-Driven Design (DDD)** architecture, multi-tenancy support, and SQLite database.

## 📚 Table of Contents

- [🏗️ Architecture](#-architecture)
- [📁 Project Structure](#-project-structure)
- [⚙️ Setup & Installation](#-setup--installation)
- [🔧 Configuration](#-configuration)
- [🗄️ Database](#-database)
- [🔐 Authentication & Authorization](#-authentication--authorization)
- [📡 API Endpoints](#-api-endpoints)
- [🧪 Testing & Development](#-testing--development)
- [🐛 Troubleshooting](#-troubleshooting)

---

## 🏗️ Architecture

### Domain-Driven Design (DDD) Pattern

The application follows **DDD** pattern with clear separation between 3 layers:

```
HTTP Request
    ↓
┌────────────────────────────────────┐
│  Application Layer                  │
│  (Controllers, Requests, Resources) │
└────────────────────────────────────┘
    ↓
┌────────────────────────────────────┐
│  Domain Layer                       │
│  (Models, Services, Business Logic) │
└────────────────────────────────────┘
    ↓
┌────────────────────────────────────┐
│  Infrastructure Layer               │
│  (Repositories, Database, Cache)    │
└────────────────────────────────────┘
    ↓
HTTP Response
```

### 1️⃣ Application Layer (`app/Application/`)

**Responsibilities:**
- Receive HTTP requests
- Validate input data (Form Requests)
- Orchestrate application logic
- Format and return API responses

**Components:**
```
Application/
├── Controllers/
│   ├── AuthController.php          # Login, Register, Logout
│   ├── ProductController.php       # CRUD Products
│   ├── CartController.php          # Cart operations
│   └── TenantController.php        # Tenant management
├── Requests/
│   ├── ProductRequest.php          # Product validation
│   └── AddToCartRequest.php        # Cart validation
└── Resources/
    ├── ProductResource.php         # Product JSON response
    ├── UserResource.php            # User JSON response
    └── CartResource.php            # Cart JSON response
```

### 2️⃣ Domain Layer (`app/Domain/`)

**Responsibilities:**
- Contains core business logic
- Define domain models (Entities)
- Implement business rules
- Define repository interfaces
- Services for business logic

**Components:**

```
Domain/
├── Cart/
│   ├── Models/Cart.php             # Cart entity
│   ├── Models/CartItem.php         # CartItem entity
│   ├── Services/CartService.php    # Cart business logic
│   └── Repositories/CartRepositoryInterface.php
│
├── Product/
│   ├── Models/Product.php          # Product entity
│   ├── Services/ProductService.php # Product business logic
│   └── Repositories/ProductRepositoryInterface.php
│
├── User/
│   └── Models/User.php             # User entity
│
└── Tenant/
    └── Models/Tenant.php           # Tenant entity
```

### 3️⃣ Infrastructure Layer (`app/Infrastructure/`)

**Responsibilities:**
- Implement concrete repository interfaces
- Database queries and logic
- Cache management
- External services integration
- Technical utilities

**Components:**
```
Infrastructure/
├── Persistence/
│   └── Repositories/
│       ├── CartRepository.php      # Database cart operations
│       ├── ProductRepository.php   # Database product operations
│       └── UserRepository.php      # Database user operations
└── Cache/
    └── ProductCache.php            # Product caching logic
```

---

## 📁 Project Structure

```
backend/
├── app/
│   ├── Application/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CartController.php
│   │   │   └── TenantController.php
│   │   ├── Requests/
│   │   │   ├── ProductRequest.php
│   │   │   └── AddToCartRequest.php
│   │   └── Resources/
│   │       ├── ProductResource.php
│   │       ├── UserResource.php
│   │       └── CartResource.php
│   ├── Domain/
│   │   ├── Cart/
│   │   │   ├── Models/Cart.php
│   │   │   ├── Models/CartItem.php
│   │   │   ├── Services/CartService.php
│   │   │   └── Repositories/CartRepositoryInterface.php
│   │   ├── Product/
│   │   │   ├── Models/Product.php
│   │   │   ├── Services/ProductService.php
│   │   │   └── Repositories/ProductRepositoryInterface.php
│   │   ├── User/
│   │   │   └── Models/User.php
│   │   └── Tenant/
│   │       └── Models/Tenant.php
│   ├── Infrastructure/
│   │   └── Persistence/
│   │       └── Repositories/
│   │           ├── CartRepository.php
│   │           ├── ProductRepository.php
│   │           └── UserRepository.php
│   ├── Models/
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── RepositoryServiceProvider.php
│   └── Http/Controllers/
├── bootstrap/
│   ├── app.php                     # Application bootstrap & middleware
│   ├── cache/
│   └── providers.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── permission.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── services.php
│   ├── session.php
│   └── tenancy.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_01_15_000001_create_products_table.php
│   │   ├── 2026_01_15_000002_add_slug_to_products_table.php
│   │   ├── 2026_01_16_000000_create_carts_table.php
│   │   ├── 2026_01_16_000001_create_cart_items_table.php
│   │   └── tenant/
│   │       └── ...
│   └── seeders/
│       └── DatabaseSeeder.php      # Test data
├── routes/
│   ├── web.php                     # Main API routes
│   └── tenant.php
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
├── tests/
│   ├── Feature/
│   ├── Unit/
│   └── TestCase.php
├── artisan                         # Artisan CLI
├── composer.json
├── vite.config.js
└── phpunit.xml
```

---

## ⚙️ Setup & Installation

### Prerequisites

- **PHP** 8.2 or higher
- **Composer** (PHP package manager)
- **Node.js & NPM** (for asset compilation)
- **SQLite** or MySQL

### Installation Steps

#### 1. Clone & Navigate
```bash
cd /path/to/task-mr-ahmad/backend
```

#### 2. Install PHP Dependencies
```bash
composer install
```

#### 3. Environment Configuration
```bash
cp .env.example .env
```

#### 4. Generate Application Key
```bash
php artisan key:generate
```

#### 5. Database Setup
```bash
# Create database file (SQLite)
touch database/database.sqlite

# Run migrations & seeders
php artisan migrate:fresh --seed
```

#### 6. Build Frontend Assets (Optional)
```bash
npm install
npm run build
```

#### 7. Start Development Server
```bash
php artisan serve
# Server runs at http://localhost:8000
```

#### 8. Verify Installation
```bash
# Check routes
php artisan route:list

# Interactive Tinker shell
php artisan tinker
>>> App\Domain\Product\Models\Product::count()
10  // Should show 10 seeded products
```

---

## 🔧 Configuration

### Database Configuration (`.env`)

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_db
DB_USERNAME=root
DB_PASSWORD=
```

### CORS Configuration

CORS is already configured in `bootstrap/app.php`:

```php
$middleware->api(prepend: [
    \Illuminate\Http\Middleware\HandleCors::class,
]);
```

Frontend can access from:
- `http://localhost:5173` (Vite dev server)
- `http://localhost:5174` (Fallback port)

### Sanctum Token Configuration

API authentication uses Laravel Sanctum. Token configuration:

```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1')),
'guard' => ['web'],
```

### Permission & Role Configuration

**Available Roles:**
- `admin` - Full access to manage products
- `customer` - Regular user for shopping

**Permission middleware:**
```php
// Protected route example
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('products', ProductController::class);
});
```

---

## 🗄️ Database

### Database Schema

#### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Products Table
```sql
CREATE TABLE products (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(8,2) NOT NULL,
    stock INT DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    is_featured BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Carts Table
```sql
CREATE TABLE carts (
    id INT PRIMARY KEY,
    user_id INT FOREIGN KEY,
    session_id VARCHAR(255),
    status ENUM('active', 'completed', 'abandoned'),
    subtotal DECIMAL(8,2) DEFAULT 0,
    tax DECIMAL(8,2) DEFAULT 0,
    shipping DECIMAL(8,2) DEFAULT 0,
    total DECIMAL(8,2) DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Cart Items Table
```sql
CREATE TABLE cart_items (
    id INT PRIMARY KEY,
    cart_id INT FOREIGN KEY,
    product_id INT FOREIGN KEY,
    quantity INT DEFAULT 1,
    price DECIMAL(8,2) NOT NULL,
    subtotal DECIMAL(8,2) NOT NULL,
    product_snapshot JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Migrations

All migrations are managed via Artisan commands:

```bash
# Create new migration
php artisan make:migration create_table_name --table=table_name

# Run migrations
php artisan migrate

# Rollback latest
php artisan migrate:rollback

# Reset & seed
php artisan migrate:fresh --seed
```

### Seeding

Seeder file: `database/seeders/DatabaseSeeder.php`

Generated test data:
- **1 Admin User** (credentials: admin@example.com / password)
- **3 Customer Users** (customer1@example.com, customer2@example.com, etc.)
- **10 Products** (with various categories and prices)

```bash
# Run seeder
php artisan db:seed

# Or with migrations
php artisan migrate:fresh --seed
```

---

## 🔐 Authentication & Authorization

### Authentication Flow

```
Register / Login Request
    ↓
POST /api/auth/register or /api/auth/login
    ↓
Validate credentials
    ↓
Generate Sanctum token
    ↓
Return: { token, user_data, roles }
    ↓
Store token in frontend (localStorage)
    ↓
Send token in Authorization header for subsequent requests
```

### Authentication Endpoints

#### Register
```bash
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}

# Response: 201 Created
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "roles": ["customer"]
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

#### Login
```bash
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}

# Response: 200 OK
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "roles": ["customer"]
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

#### Get Current User
```bash
GET /api/auth/me
Authorization: Bearer {token}

# Response: 200 OK
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "roles": ["customer"]
}
```

#### Logout
```bash
POST /api/auth/logout
Authorization: Bearer {token}

# Response: 200 OK
{
    "message": "Logged out successfully"
}
```

### Authorization (Roles & Permissions)

**Available Roles:**
- `admin` - Can manage products, view analytics
- `customer` - Can browse products, cart, checkout

**Middleware:**
```php
// Protected route - admin only
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('products', ProductController::class)->except(['show', 'index']);
});

// Protected route - authenticated users only
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/items', [CartController::class, 'addItem']);
});
```

---

## 📡 API Endpoints

### Product Endpoints

#### Get All Products (Public)
```bash
GET /api/products
Query Parameters: ?is_active=1&is_featured=1

Response:
{
    "data": [
        {
            "id": 1,
            "name": "Product Name",
            "slug": "product-name",
            "description": "Product description",
            "price": 50000,
            "stock": 100,
            "is_active": true,
            "is_featured": true
        },
        ...
    ]
}
```

#### Get Product Detail (Public)
```bash
GET /api/products/{id}

Response:
{
    "data": {
        "id": 1,
        "name": "Product Name",
        "slug": "product-name",
        "description": "Product description",
        "price": 50000,
        "stock": 100,
        "is_active": true,
        "is_featured": true
    }
}
```

#### Create Product (Admin Only)
```bash
POST /api/products
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "name": "New Product",
    "description": "Product description",
    "price": 75000,
    "stock": 50
}

Response: 201 Created
```

#### Update Product (Admin Only)
```bash
PUT /api/products/{id}
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "name": "Updated Product",
    "price": 80000
}

Response: 200 OK
```

#### Delete Product (Admin Only)
```bash
DELETE /api/products/{id}
Authorization: Bearer {admin_token}

Response: 204 No Content
```

### Cart Endpoints

#### Get Cart
```bash
GET /api/cart
Authorization: Bearer {token}

Response:
{
    "data": {
        "id": 1,
        "user_id": 1,
        "status": "active",
        "items": [
            {
                "id": 1,
                "product_id": 1,
                "quantity": 2,
                "price": 50000,
                "subtotal": 100000
            }
        ],
        "subtotal": 100000,
        "tax": 10000,
        "shipping": 20000,
        "total": 130000
    }
}
```

#### Add Item to Cart
```bash
POST /api/cart/items
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 2
}

Response: 201 Created
{
    "message": "Item added to cart",
    "data": {
        "id": 1,
        "product_id": 1,
        "quantity": 2,
        "subtotal": 100000
    }
}
```

#### Update Cart Item
```bash
PUT /api/cart/items/{itemId}
Authorization: Bearer {token}
Content-Type: application/json

{
    "quantity": 3
}

Response: 200 OK
```

#### Remove Item from Cart
```bash
DELETE /api/cart/items/{itemId}
Authorization: Bearer {token}

Response: 204 No Content
```

#### Clear Cart
```bash
POST /api/cart/clear
Authorization: Bearer {token}

Response: 200 OK
{
    "message": "Cart cleared successfully"
}
```

---

## 🧪 Testing & Development

### Using Tinker (Interactive Shell)

```bash
php artisan tinker
```

**Example commands:**

```php
# Check users
App\Domain\User\Models\User::all()

# Check products
App\Domain\Product\Models\Product::active()->get()

# Create user
App\Domain\User\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password')
])

# Check cart items
App\Domain\Cart\Models\Cart::with('items')->first()
```

### Manual API Testing with cURL

```bash
# Register user
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'

# Get products
curl http://localhost:8000/api/products

# Add to cart (requires token)
curl -X POST http://localhost:8000/api/cart/items \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

### Unit Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/AuthTest.php

# With coverage
php artisan test --coverage
```

Test files are located in `tests/Feature/` and `tests/Unit/`

---

## 🐛 Troubleshooting

### Common Issues

**1. "No such table: users"**
- Solution: Run `php artisan migrate`

**2. "CORS Error from Frontend"**
- Check `bootstrap/app.php` CORS configuration
- Ensure frontend URL is in SANCTUM_STATEFUL_DOMAINS

**3. "401 Unauthorized on Protected Routes"**
- Verify token is being sent: `Authorization: Bearer {token}`
- Check token validity: `php artisan tinker` → `App\Models\PersonalAccessToken::find(1)`

**4. "500 - Model not found"**
- Check if migration ran: `php artisan migrate:status`
- Verify model namespace in controller

**5. "Port 8000 already in use"**
- Use different port: `php artisan serve --port=8001`
- Or kill existing process: `lsof -i :8000 | grep -v PID | awk '{print $2}' | xargs kill -9`

### Debug Tips

```bash
# Enable query logging
php artisan tinker
>>> DB::enableQueryLog()
>>> // Run your queries
>>> dd(DB::getQueryLog())

# Check all routes
php artisan route:list

# Check middleware
php artisan middleware:list

# Clear cache
php artisan cache:clear
php artisan config:clear
```

---

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Domain-Driven Design](https://martinfowler.com/bliki/DomainDrivenDesign.html)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Stancl Tenancy](https://tenancyforlaravel.com/)

---

**Last Updated:** January 2026
**Maintained by:** Development Team
