<?php

namespace Modules\Flight\Adapters;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Flight\Adapters\Contracts\FlightSupplierAdapterInterface;
use Modules\Flight\DTOs\FlightBookingResult;
use Modules\Flight\DTOs\FlightSearchResult;
use Modules\Flight\Models\FlightSupplier;

abstract class BaseFlightSupplierAdapter implements FlightSupplierAdapterInterface
{
    protected FlightSupplier $supplier;

    protected array $config;

    protected string $baseUrl;

    protected int $timeout = 30;

    public function __construct(FlightSupplier $supplier)
    {
        $this->supplier = $supplier;
        $this->config = $supplier->configuration ?? [];
        $this->baseUrl = $this->config['base_url'] ?? '';
    }

    public function getCode(): string
    {
        return $this->supplier->code;
    }

    public function getName(): string
    {
        return $this->supplier->name;
    }

    public function isAvailable(): bool
    {
        return $this->supplier->is_active && ! empty($this->baseUrl);
    }

    /**
     * Make HTTP request to supplier API.
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->$method($this->baseUrl.$endpoint, $data);

            if ($response->failed()) {
                Log::error('Supplier API error', [
                    'supplier' => $this->getCode(),
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'error' => $response->json('error.message') ?? 'API request failed',
                    'status' => $response->status(),
                ];
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::error('Supplier API exception', [
                'supplier' => $this->getCode(),
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get request headers.
     */
    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Transform supplier response to FlightSearchResult.
     */
    abstract protected function transformSearchResult(array $data): FlightSearchResult;

    /**
     * Transform supplier response to FlightBookingResult.
     */
    abstract protected function transformBookingResult(array $data): FlightBookingResult;

    /**
     * Log API interaction.
     */
    protected function logApiCall(string $action, array $request, array $response): void
    {
        Log::info("Flight API: {$action}", [
            'supplier' => $this->getCode(),
            'request' => $request,
            'success' => $response['success'] ?? false,
        ]);
    }

    // Default implementations
    public function getFlightDetails(string $offerId): ?array
    {
        return null;
    }

    public function getFareRules(string $offerId): array
    {
        return [];
    }

    public function getSeatMap(string $flightNumber, string $date): array
    {
        return [];
    }

    public function getBookingStatus(string $supplierBookingId): array
    {
        return ['status' => 'unknown'];
    }
}
