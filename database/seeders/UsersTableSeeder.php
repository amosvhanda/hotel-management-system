<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Starter accounts on our own domain (config app.domain, e.g. hotel.synccode.co.zw).
     *
     *   Admin: ADMIN_EMAIL / ADMIN_PASSWORD from .env (production sets these),
     *          else admin@<domain> / "password" for local development.
     *   Guest: guest@<domain> / "password" — a demo guest account.
     *
     * Older installs had admin@gmail.com / user@gmail.com; those are moved to the new
     * addresses (the admin also gets ADMIN_PASSWORD when it is set). Safe to run again.
     */
    public function run(): void
    {
        $domain = config('app.domain');
        $adminEmail = env('ADMIN_EMAIL') ?: "admin@{$domain}";
        $adminPassword = env('ADMIN_PASSWORD');
        $guestEmail = "guest@{$domain}";

        $this->moveLegacy('admin@gmail.com', $adminEmail, $adminPassword);
        $this->moveLegacy('user@gmail.com', $guestEmail);

        // Only create the starter admin when the site has none (so renaming it in Admin
        // later never brings a second one back on the next deploy).
        if (! User::where('role', 'admin')->where('email', '!=', $adminEmail)->exists()) {
            $admin = User::firstOrCreate(
                ['email' => $adminEmail],
                ['name' => 'Admin', 'password' => Hash::make($adminPassword ?: 'password'), 'role' => 'admin', 'status' => 'active']
            );
            $admin->assignRole('Super Admin');
        }

        User::firstOrCreate(
            ['email' => $guestEmail],
            ['name' => 'Guest User', 'password' => Hash::make('password'), 'role' => 'user', 'status' => 'active']
        );
    }

    private function moveLegacy(string $old, string $new, ?string $password = null): void
    {
        $user = User::where('email', $old)->first();
        if (! $user || User::where('email', $new)->exists()) {
            return;
        }

        $user->email = $new;
        if ($password) {
            $user->password = Hash::make($password);
        }
        $user->save();
    }
}
