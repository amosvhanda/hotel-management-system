# Laravel upgrade runbook (10 -> 11 -> 12)

Branch first: `git checkout -b upgrade-laravel-12` and commit everything.

## Step A: Laravel 11 (composer.json already set)
```
php -v                      # must be 8.2 or newer (Herd: switch PHP version)
composer update             # resolves Laravel 11, Sanctum 4, Spatie permission 6, DomPDF 3, PHPUnit 11
php artisan optimize:clear
php artisan migrate
php artisan test
php artisan route:list      # check 'roles' and 'permission' middleware still appear on admin routes
```
If `composer update` complains about a package, paste the full message.

## Step B: Laravel 12 (only after Step A is green)
In composer.json change `"laravel/framework": "^11.0"` to `"^12.0"`, then:
```
composer update
php artisan optimize:clear
php artisan test
```

## Manual check after each step
Home, rooms, booking + test Paynow payment, invoice PDF, admin login/dashboard,
permission export/import (Excel), gallery upload (Intervention Image), notifications.

## What was changed in code
- `Booking::stayNights()` replaces every `diffInDays()` call (Carbon 3 returns signed floats).
- `Kernel` registers Spatie middleware under the namespace the installed version uses.
- composer.json pins `nesbot/carbon ^2.72.6` for now; move to Carbon 3 as a later task.
- Test data providers use the `#[DataProvider]` attribute (PHPUnit 11/12).
- The old Kernel/Provider structure is kept on purpose; the slim skeleton can come later.
