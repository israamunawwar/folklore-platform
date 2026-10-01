# 🏺 Folklore Platform — Heritage Marketplace

An e-commerce platform for traditional heritage items — clothing & jewelry, folk food, and books — with a moderation workflow so every item and review is checked before it goes live.

## ✨ Features

**Customers**
- Browse approved items by category and open an item's detail page
- Like items and leave a 1–5 ★ review (reviews appear after moderation)
- Session-based shopping cart with live stock limits
- Checkout with cash on delivery (phone, governorate/city and address validation)
- Own register / login pages

**Admin panel (Filament 3, staff only)**
- Role-based access: **admin**, **moderator**, **publisher**
- Publishers add items (pending) and edit only their own; moderators/admins approve or reject them
- Review moderation: pending / approved / rejected
- Order management (status changes; cancelling an order restores stock once)
- User management (admin only)
- Items are soft-deleted, so past orders keep their history

**Backend design**
- Stock is deducted **once**, at checkout, inside a DB transaction with row locks
- Order prices are read from the database, never from the session
- Policies for every admin resource; `canAccessPanel` enforced via `FilamentUser`
- Feature tests for checkout/stock, validation, access control and auth

## 🛠️ Tech Stack

Laravel 12 · PHP 8.2+ · Filament 3 · Blade + Tailwind (CDN) · SQLite (default) / MySQL

## 🚀 Getting Started

```bash
git clone https://github.com/israamunawwar/folklore-platform.git
cd folklore-platform
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # Windows: type nul > database\database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

- Store: http://localhost:8000
- Admin panel: http://localhost:8000/admin (staff login)

### Seeded staff accounts

| Role | Email |
|---|---|
| Admin | admin@admin.com |
| Moderator | moderator@test.com |
| Publisher | publisher@test.com |

The password comes from `SEED_PASSWORD` in `.env`. If it is empty, the seeder uses `password` in local environments and **refuses to run in production**.

## ✅ Tests

```bash
php artisan test
```

## 🗺️ Roadmap

- REST API (Laravel Sanctum is already installed)
- Persist the cart in the database
- Customer order history page and order status notifications
- Sales / low-stock dashboard widgets

## 👥 Team

- **Mahmoud Ghannam**
- **Israa Munawwar**
