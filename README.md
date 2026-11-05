# Hotel Management System

A complete hotel website and back office built with Laravel 10, designed and developed by **Amos Vhanda**.

Guests can browse rooms, check live availability, book online and pay at the hotel or with **EcoCash** or **NetOne OneMoney** through Paynow. Staff get a full admin panel for bookings, rooms, payments, content and user roles.

---

## Features

**Guest website**
- Room listing, room details and live availability search (no overbooking)
- Online checkout: *Pay at hotel*, *EcoCash* or *NetOne OneMoney* (Paynow express checkout)
- Live payment-status page while the guest approves on their phone
- Guest account: overview, upcoming stay, booking history, PDF invoices, profile and password
- Services, gallery, blog with comments, team, testimonials, about and contact pages
- Hotel name, logo, contact details, social links and SEO tags all managed from Site Settings

**Admin panel**
- Dashboard: arrivals/departures today, occupancy, monthly revenue, unpaid balance, 12-month chart
- Bookings: edit dates, assign room numbers, confirm (emails the guest), mark paid, PDF invoice
- Rooms, room types, room numbers, services (with images), team, testimonials, gallery, blog, comments
- Booking reports by date, contact messages, notifications
- Roles & permissions (Super Admin, Admin, Manager, Receptionist) and staff accounts
- SMTP and site settings

---

## Requirements

- PHP 8.1+ with `pdo_mysql`, `gd`, `mbstring`, `xml`, `zip`
- MySQL 5.7+ / MariaDB 10.3+
- Composer

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
# set DB_DATABASE / DB_USERNAME / DB_PASSWORD in .env
php artisan migrate --seed
php artisan serve
```

Seeded accounts (change these passwords immediately):

| Role        | Email             | Password   |
|-------------|-------------------|------------|
| Super Admin | admin@gmail.com   | `password` |
| Guest       | user@gmail.com    | `password` |

Staff sign in at `/admin/login`.

### Upgrading an existing database

```bash
php artisan migrate
```

This adds the services table and images, service permissions, Paynow tracking fields and the permission `group_name` column if they are missing. Existing data is kept.

---

## Paynow (EcoCash / NetOne OneMoney)

1. Create an integration in your Paynow merchant dashboard.
2. Add the credentials to `.env`:

   ```
   PAYNOW_INTEGRATION_ID=your-id
   PAYNOW_INTEGRATION_KEY=your-key
   ```

3. Run `php artisan config:clear`.
4. Your site must be reachable from the internet so Paynow can post results to `https://your-domain/paynow/result`.

How it works: the booking is saved first, then a payment prompt is sent to the guest's phone. When Paynow confirms, the booking is marked **Paid** and admins are notified. Cancelled or failed payments release the room dates automatically. Until the keys are set, the mobile-money options show a friendly "not available" message and guests can still book with *Pay at hotel*.

## Email

Booking confirmations use the SMTP details saved in **Admin → Settings → SMTP**. If those are empty, the `MAIL_*` values in `.env` are used.

---

## Demo data (Zimbabwe)

```bash
php artisan db:seed --class=ZimbabweDemoSeeder
```

Fills the site with **fictional** sample content: *Mopane Heights Hotel* in Harare, rooms priced in USD, Zimbabwean staff names, guest reviews, blog posts and services. Safe to run more than once; it never changes logins or passwords. **Replace the sample hotel details, team and reviews with your real ones before going live.**

---

## Automated tests

```bash
php artisan test
```

85 tests (in-memory SQLite, nothing touches your real data) covering: public pages and links, availability and overbooking, the booking and checkout flow, EcoCash/OneMoney Paynow callbacks (paid, forged, underpaid, cancelled), access control, admin actions, emails and the demo seeder. Run them before every deployment.

---

## Deploying to production

1. **Server:** PHP 8.1+ (`pdo_mysql`, `gd`, `mbstring`, `xml`, `zip`, `fileinfo`), MySQL/MariaDB, HTTPS certificate.
2. **Upload the code** (without `vendor/`, `node_modules/`, `.env`) and run `composer install --no-dev --optimize-autoloader`.
3. **Point the web root at `public/`** (never at the project folder).
4. **Create `.env`** from `.env.example` and set:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.co.zw
   DB_CONNECTION=mysql   # plus DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
   PAYNOW_INTEGRATION_ID=...
   PAYNOW_INTEGRATION_KEY=...
   ```
   then `php artisan key:generate` (new installs only).
5. **Database:** `php artisan migrate --force` (add `--seed` on a brand-new database), or import your existing data first.
6. **Permissions:** `storage/`, `bootstrap/cache/` and `public/upload/` must be writable by the web server.
7. **Cache for speed:** `php artisan optimize` (run `php artisan optimize:clear` whenever you change `.env` or deploy new code).
8. **Paynow:** in the Paynow dashboard, make sure your site is reachable at `https://your-domain/paynow/result`.
9. **Email:** enter real SMTP details in **Admin → Settings → SMTP** and send yourself a test booking confirmation.
10. **Backups:** schedule a daily database backup (e.g. `mysqldump`) and keep a copy of `public/upload/`.
11. **After going live:** change the seeded passwords, remove sample accounts you don't use, and check `storage/logs/laravel.log` for errors.

---

© Amos Vhanda. All rights reserved.
