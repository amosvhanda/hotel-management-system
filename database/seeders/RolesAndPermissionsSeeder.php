<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /** group name => permissions (used by the sidebar and route middleware) */
    public const PERMISSIONS = [
        'Team'                => ['team.menu', 'team.all', 'team.add', 'team.edit', 'team.delete'],
        'Book Area'           => ['bookarea.menu', 'update.bookarea'],
        'Service'             => ['service.menu', 'service.all', 'service.add', 'service.edit', 'service.delete'],
        'Manage Room'         => ['room.type.menu', 'room.type'],
        'Booking'             => ['booking.menu', 'booking.list', 'booking.add'],
        'RoomList'            => ['room.list.menu', 'room.list'],
        'Setting'             => ['setting.menu'],
        'Tesimonial'          => ['tesimonial.menu', 'tesimonial.all', 'tesimonial.add', 'tesimonial.edit', 'tesimonial.delete'],
        'Blog'                => ['blog.menu', 'blog.category', 'blog.post.list'],
        'Manage Comment'      => ['comment.menu'],
        'Booking Report'      => ['booking.report.menu'],
        'Hotel Gallery'       => ['gallery.menu'],
        'Contact Message'     => ['contact.message.menu'],
        'Role and Permission' => ['role.permission.menu'],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $group => $names) {
            foreach ($names as $name) {
                Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web'], ['group_name' => $group]);
            }
        }

        $all = Permission::pluck('name')->all();
        $front = ['booking.menu', 'booking.list', 'booking.add', 'room.list.menu', 'room.list', 'contact.message.menu'];

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web'])->syncPermissions($all);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web'])
            ->syncPermissions(array_values(array_diff($all, ['role.permission.menu'])));
        Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web'])
            ->syncPermissions(array_merge($front, ['booking.report.menu', 'room.type.menu', 'room.type', 'service.menu', 'service.all']));
        Role::firstOrCreate(['name' => 'Receptionist', 'guard_name' => 'web'])->syncPermissions($front);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
