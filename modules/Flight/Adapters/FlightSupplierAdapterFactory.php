<?php

namespace Modules\Flight\Adapters;

use Modules\Flight\Adapters\Contracts\FlightSupplierAdapterInterface;
use Modules\Flight\Models\FlightSupplier;

class FlightSupplierAdapterFactory
{
    /**
     * Registered adapter classes.
     */
    protected static array $adapters = [
        'amadeus' => Suppliers\AmadeusAdapter::class,
        'sabre' => Suppliers\SabreAdapter::class,
        // 'travelport' => Suppliers\TravelportAdapter::class,
    ];

    /**
     * Create adapter for a supplier.
     */
    public static function make(FlightSupplier $supplier): ?FlightSupplierAdapterInterface
    {
        $code = strtolower($supplier->code);

        if (! isset(self::$adapters[$code])) {
            return null;
        }

        $adapterClass = self::$adapters[$code];

        return new $adapterClass($supplier);
    }

    /**
     * Create adapter by supplier code.
     */
    public static function makeFromCode(string $code): ?FlightSupplierAdapterInterface
    {
        $supplier = FlightSupplier::where('code', $code)->where('is_active', true)->first();

        if (! $supplier) {
            return null;
        }

        return self::make($supplier);
    }

    /**
     * Get all active supplier adapters.
     */
    public static function all(): array
    {
        $adapters = [];

        $suppliers = FlightSupplier::where('is_active', true)->get();

        foreach ($suppliers as $supplier) {
            $adapter = self::make($supplier);
            if ($adapter) {
                $adapters[] = $adapter;
            }
        }

        return $adapters;
    }

    /**
     * Register a new adapter.
     */
    public static function register(string $code, string $adapterClass): void
    {
        self::$adapters[strtolower($code)] = $adapterClass;
    }

    /**
     * Check if adapter exists for supplier.
     */
    public static function has(string $code): bool
    {
        return isset(self::$adapters[strtolower($code)]);
    }
}
