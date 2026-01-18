<?php

namespace Modules\Flight\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FlightAclSeeder extends Seeder
{
    protected array $permissions = [
        // Flight Management
        'flight.view',
        'flight.create',
        'flight.edit',
        'flight.delete',
        'flight.restore',

        // Airport Management
        'flight.airport.view',
        'flight.airport.create',
        'flight.airport.edit',
        'flight.airport.delete',

        // Airline Management
        'flight.airline.view',
        'flight.airline.create',
        'flight.airline.edit',
        'flight.airline.delete',

        // Aircraft Management
        'flight.aircraft.view',
        'flight.aircraft.create',
        'flight.aircraft.edit',
        'flight.aircraft.delete',

        // Fare Class Management
        'flight.fare-class.view',
        'flight.fare-class.create',
        'flight.fare-class.edit',
        'flight.fare-class.delete',

        // Fare/Inventory Management
        'flight.fare.view',
        'flight.fare.create',
        'flight.fare.edit',
        'flight.fare.delete',

        // Booking Management
        'flight.booking.view',
        'flight.booking.create',
        'flight.booking.edit',
        'flight.booking.cancel',
        'flight.booking.delete',
        'flight.booking.ticket',
        'flight.booking.void',

        // Passenger Management
        'flight.passenger.view',
        'flight.passenger.edit',

        // Payment Management
        'flight.payment.view',
        'flight.payment.create',
        'flight.payment.refund',
        'flight.payment.verify',

        // Cancellation & Exchange
        'flight.cancellation.view',
        'flight.cancellation.create',
        'flight.cancellation.approve',
        'flight.exchange.view',
        'flight.exchange.create',
        'flight.exchange.approve',

        // Review Management
        'flight.review.view',
        'flight.review.moderate',
        'flight.review.respond',
        'flight.review.delete',

        // FAQ Management
        'flight.faq.view',
        'flight.faq.create',
        'flight.faq.edit',
        'flight.faq.delete',

        // Supplier Management
        'flight.supplier.view',
        'flight.supplier.create',
        'flight.supplier.edit',
        'flight.supplier.delete',
        'flight.supplier.sync',

        // Reports & Analytics
        'flight.report.view',
        'flight.report.export',
        'flight.search-log.view',
        'flight.popular-route.view',
        'flight.popular-route.edit',
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

        // Assign all flight permissions to super-admin
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($this->permissions);
        }

        // Create flight-specific roles
        $flightManager = Role::firstOrCreate([
            'name' => 'flight-manager',
            'guard_name' => $guard,
        ]);

        $flightManager->syncPermissions([
            'flight.view',
            'flight.create',
            'flight.edit',
            'flight.airport.view',
            'flight.airport.create',
            'flight.airport.edit',
            'flight.airline.view',
            'flight.airline.create',
            'flight.airline.edit',
            'flight.aircraft.view',
            'flight.aircraft.create',
            'flight.aircraft.edit',
            'flight.fare-class.view',
            'flight.fare-class.create',
            'flight.fare-class.edit',
            'flight.fare.view',
            'flight.fare.create',
            'flight.fare.edit',
            'flight.booking.view',
            'flight.booking.create',
            'flight.booking.edit',
            'flight.booking.cancel',
            'flight.booking.ticket',
            'flight.passenger.view',
            'flight.passenger.edit',
            'flight.payment.view',
            'flight.payment.create',
            'flight.payment.refund',
            'flight.payment.verify',
            'flight.cancellation.view',
            'flight.cancellation.create',
            'flight.cancellation.approve',
            'flight.exchange.view',
            'flight.exchange.create',
            'flight.exchange.approve',
            'flight.review.view',
            'flight.review.moderate',
            'flight.review.respond',
            'flight.faq.view',
            'flight.faq.create',
            'flight.faq.edit',
            'flight.supplier.view',
            'flight.supplier.sync',
            'flight.report.view',
            'flight.report.export',
            'flight.search-log.view',
            'flight.popular-route.view',
            'flight.popular-route.edit',
        ]);

        // Flight booking agent role
        $flightAgent = Role::firstOrCreate([
            'name' => 'flight-agent',
            'guard_name' => $guard,
        ]);

        $flightAgent->syncPermissions([
            'flight.view',
            'flight.airport.view',
            'flight.airline.view',
            'flight.aircraft.view',
            'flight.fare-class.view',
            'flight.fare.view',
            'flight.booking.view',
            'flight.booking.create',
            'flight.booking.edit',
            'flight.booking.cancel',
            'flight.passenger.view',
            'flight.passenger.edit',
            'flight.payment.view',
            'flight.payment.create',
            'flight.cancellation.view',
            'flight.cancellation.create',
            'flight.exchange.view',
            'flight.exchange.create',
            'flight.faq.view',
        ]);

        // Ticketing agent role
        $ticketingAgent = Role::firstOrCreate([
            'name' => 'ticketing-agent',
            'guard_name' => $guard,
        ]);

        $ticketingAgent->syncPermissions([
            'flight.view',
            'flight.airport.view',
            'flight.airline.view',
            'flight.booking.view',
            'flight.booking.edit',
            'flight.booking.ticket',
            'flight.booking.void',
            'flight.passenger.view',
            'flight.passenger.edit',
            'flight.payment.view',
        ]);
    }
}
