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

Production runs on the **same Docker + Traefik VPS as SyncCode**, at **https://hotel.synccode.co.zw**.

Full steps (DNS, GitHub secrets, first install, Paynow): see [docs/SERVER_DEPLOYMENT.md](docs/SERVER_DEPLOYMENT.md).

Summary:

1. Create DNS A record `hotel.synccode.co.zw` → the SyncCode VPS IP.
2. Add GitHub Actions secrets: `SSH_HOST`, `SSH_USER`, `SSH_PRIVATE_KEY`, `SSH_PATH=/opt/apps/hotel`, `DOMAIN=hotel.synccode.co.zw`.
3. Run **Actions → Deploy to server** once with `first_install=true` (creates `.env`, MySQL, migrate, seed). Later pushes to `main` rebuild and migrate automatically.
4. On the server `.env`, set `PAYNOW_INTEGRATION_ID` / `PAYNOW_INTEGRATION_KEY`, then after HTTPS works set `APP_URL=https://hotel.synccode.co.zw` and `SESSION_SECURE_COOKIE=true`.
5. Change seeded passwords, configure SMTP in **Admin → Settings**, and point Paynow at `https://hotel.synccode.co.zw/paynow/result`.

---

© Amos Vhanda. All rights reserved.
