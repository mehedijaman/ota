<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HotelAclSeeder extends Seeder
{
    protected array $permissions = [
        // Hotel Management
        'hotel.view',
        'hotel.create',
        'hotel.edit',
        'hotel.delete',
        'hotel.restore',

        // Room Management
        'hotel.room.view',
        'hotel.room.create',
        'hotel.room.edit',
        'hotel.room.delete',

        // Rate Management
        'hotel.rate.view',
        'hotel.rate.create',
        'hotel.rate.edit',
        'hotel.rate.delete',

        // Inventory Management
        'hotel.inventory.view',
        'hotel.inventory.edit',

        // Booking Management
        'hotel.booking.view',
        'hotel.booking.create',
        'hotel.booking.edit',
        'hotel.booking.cancel',
        'hotel.booking.delete',

        // Payment Management
        'hotel.payment.view',
        'hotel.payment.create',
        'hotel.payment.refund',

        // Review Management
        'hotel.review.view',
        'hotel.review.moderate',
        'hotel.review.respond',
        'hotel.review.delete',

        // Supplier Management
        'hotel.supplier.view',
        'hotel.supplier.create',
        'hotel.supplier.edit',
        'hotel.supplier.delete',

        // Reports
        'hotel.report.view',
        'hotel.report.export',
    ];

    public function run(): void
    {
        $guard = 'web';

        // Create permissions
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guard,
            ]);
        }

        // Assign all hotel permissions to super-admin
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($this->permissions);
        }

        // Create hotel-specific roles if needed
        $hotelManager = Role::firstOrCreate([
            'name' => 'hotel-manager',
            'guard_name' => $guard,
        ]);

        $hotelManager->syncPermissions([
            'hotel.view',
            'hotel.edit',
            'hotel.room.view',
            'hotel.room.create',
            'hotel.room.edit',
            'hotel.rate.view',
            'hotel.rate.create',
            'hotel.rate.edit',
            'hotel.inventory.view',
            'hotel.inventory.edit',
            'hotel.booking.view',
            'hotel.booking.create',
            'hotel.booking.edit',
            'hotel.booking.cancel',
            'hotel.payment.view',
            'hotel.payment.create',
            'hotel.review.view',
            'hotel.review.respond',
            'hotel.report.view',
        ]);

        // Booking agent role
        $bookingAgent = Role::firstOrCreate([
            'name' => 'booking-agent',
            'guard_name' => $guard,
        ]);

        $bookingAgent->syncPermissions([
            'hotel.view',
            'hotel.room.view',
            'hotel.rate.view',
            'hotel.inventory.view',
            'hotel.booking.view',
            'hotel.booking.create',
            'hotel.booking.edit',
            'hotel.payment.view',
            'hotel.payment.create',
        ]);
    }
}
