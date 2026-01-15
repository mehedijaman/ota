<?php

namespace Modules\Hotel\Adapters;

use Illuminate\Support\Facades\Log;
use Modules\Hotel\Adapters\Contracts\HotelSupplierAdapterInterface;
use Modules\Hotel\Adapters\Suppliers\AgodaAdapter;
use Modules\Hotel\Adapters\Suppliers\DemoAdapter;
use Modules\Hotel\Adapters\Suppliers\HotelbedsAdapter;
use Modules\Hotel\Adapters\Suppliers\LocalAdapter;
use Modules\Hotel\Models\HotelSupplier;

/**
 * Factory for creating hotel supplier adapters.
 *
 * This factory maintains a registry of adapter classes and creates
 * instances based on supplier type with proper credential loading.
 */
class SupplierAdapterFactory
{
    /**
     * Registry of adapter classes by supplier type.
     */
    protected static array $adapters = [
        'local' => LocalAdapter::class,
        'demo' => DemoAdapter::class,
        'hotelbeds' => HotelbedsAdapter::class,
        'agoda' => AgodaAdapter::class,
        // Add more as needed:
        // 'expedia' => ExpediaAdapter::class,
        // 'booking' => BookingComAdapter::class,
    ];

    /**
     * Create an adapter for a specific supplier.
     */
    public static function make(HotelSupplier $supplier): ?HotelSupplierAdapterInterface
    {
        $type = strtolower($supplier->type ?? 'local');

        if (! isset(self::$adapters[$type])) {
            Log::warning("No adapter registered for supplier type: {$type}");

            return null;
        }

        $adapterClass = self::$adapters[$type];

        return new $adapterClass($supplier);
    }

    /**
     * Create an adapter by supplier type code.
     */
    public static function makeByType(string $type): ?HotelSupplierAdapterInterface
    {
        $type = strtolower($type);

        if (! isset(self::$adapters[$type])) {
            return null;
        }

        // For local adapter, we don't need a supplier
        if ($type === 'local') {
            return new LocalAdapter;
        }

        // Find the supplier by type
        $supplier = HotelSupplier::where('type', $type)->active()->first();

        if (! $supplier) {
            return null;
        }

        return self::make($supplier);
    }

    /**
     * Get all active adapters.
     *
     * @return HotelSupplierAdapterInterface[]
     */
    public static function getAllActive(): array
    {
        $adapters = [];

        // Always include local adapter
        $adapters[] = new LocalAdapter;

        // Get active suppliers and create adapters
        $suppliers = HotelSupplier::active()->byPriority()->get();

        foreach ($suppliers as $supplier) {
            $adapter = self::make($supplier);
            if ($adapter && $adapter->isConfigured()) {
                $adapters[] = $adapter;
            }
        }

        return $adapters;
    }

    /**
     * Register a custom adapter.
     */
    public static function register(string $type, string $adapterClass): void
    {
        self::$adapters[strtolower($type)] = $adapterClass;
    }

    /**
     * Get registered adapter types.
     */
    public static function getRegisteredTypes(): array
    {
        return array_keys(self::$adapters);
    }
}
