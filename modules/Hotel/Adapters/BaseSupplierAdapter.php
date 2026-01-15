<?php

namespace Modules\Hotel\Adapters;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Hotel\Adapters\Contracts\HotelSupplierAdapterInterface;
use Modules\Hotel\DTOs\BookingResult;
use Modules\Hotel\DTOs\HotelAvailabilityResult;
use Modules\Hotel\Models\HotelSupplier;
use Modules\Hotel\Models\HotelSupplierCredential;

/**
 * Base adapter class for hotel supplier integrations.
 *
 * Provides common functionality for HTTP requests, error handling,
 * logging, and credential management.
 */
abstract class BaseSupplierAdapter implements HotelSupplierAdapterInterface
{
    protected ?HotelSupplier $supplier = null;

    protected ?HotelSupplierCredential $credential = null;

    protected string $environment = 'sandbox';

    protected int $timeout = 30;

    protected int $retryAttempts = 3;

    protected array $lastErrors = [];

    public function __construct(?HotelSupplier $supplier = null)
    {
        if ($supplier) {
            $this->setSupplier($supplier);
        }
    }

    /**
     * Set the supplier and load credentials.
     */
    public function setSupplier(HotelSupplier $supplier): self
    {
        $this->supplier = $supplier;
        $this->timeout = $supplier->timeout_seconds ?? 30;
        $this->retryAttempts = $supplier->retry_attempts ?? 3;

        // Load appropriate credential based on environment
        $this->credential = $this->environment === 'production'
            ? $supplier->productionCredential()
            : $supplier->sandboxCredential();

        return $this;
    }

    /**
     * Set the environment (production or sandbox).
     */
    public function setEnvironment(string $environment): self
    {
        $this->environment = $environment;

        if ($this->supplier) {
            $this->credential = $environment === 'production'
                ? $this->supplier->productionCredential()
                : $this->supplier->sandboxCredential();
        }

        return $this;
    }

    /**
     * Check if the adapter has valid credentials.
     */
    public function isConfigured(): bool
    {
        return $this->credential !== null && $this->credential->is_active;
    }

    /**
     * Get the base URL for API requests.
     */
    abstract protected function getBaseUrl(): string;

    /**
     * Get authentication headers for the supplier.
     */
    abstract protected function getAuthHeaders(): array;

    /**
     * Create a configured HTTP client.
     */
    protected function httpClient(): PendingRequest
    {
        return Http::baseUrl($this->getBaseUrl())
            ->timeout($this->timeout)
            ->retry($this->retryAttempts, 1000)
            ->withHeaders($this->getAuthHeaders())
            ->acceptJson()
            ->asJson();
    }

    /**
     * Make a GET request to the supplier API.
     */
    protected function get(string $endpoint, array $query = []): array
    {
        try {
            $response = $this->httpClient()->get($endpoint, $query);

            if ($response->successful()) {
                $this->credential?->markSuccess();

                return $response->json();
            }

            $this->handleError($response->status(), $response->json());

            return [];
        } catch (\Exception $e) {
            $this->logError('GET', $endpoint, $e);
            $this->credential?->markFailure($e->getMessage());

            return [];
        }
    }

    /**
     * Make a POST request to the supplier API.
     */
    protected function post(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->httpClient()->post($endpoint, $data);

            if ($response->successful()) {
                $this->credential?->markSuccess();

                return $response->json();
            }

            $this->handleError($response->status(), $response->json());

            return [];
        } catch (\Exception $e) {
            $this->logError('POST', $endpoint, $e);
            $this->credential?->markFailure($e->getMessage());

            return [];
        }
    }

    /**
     * Handle API error responses.
     */
    protected function handleError(int $status, ?array $response): void
    {
        $error = [
            'status' => $status,
            'message' => $response['error'] ?? $response['message'] ?? 'Unknown error',
            'response' => $response,
        ];

        $this->lastErrors[] = $error;
        $this->credential?->markFailure($error['message']);

        Log::error("Hotel Supplier API Error [{$this->getSupplierCode()}]", $error);
    }

    /**
     * Log an exception.
     */
    protected function logError(string $method, string $endpoint, \Exception $e): void
    {
        Log::error("Hotel Supplier Exception [{$this->getSupplierCode()}]", [
            'method' => $method,
            'endpoint' => $endpoint,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    /**
     * Get the last errors.
     */
    public function getLastErrors(): array
    {
        return $this->lastErrors;
    }

    /**
     * Clear stored errors.
     */
    public function clearErrors(): void
    {
        $this->lastErrors = [];
    }

    /**
     * Log a sync operation.
     */
    protected function logSync(string $operation, int $count, ?string $error = null): void
    {
        if (! $this->supplier) {
            return;
        }

        $this->supplier->syncLogs()->create([
            'sync_type' => $operation,
            'status' => $error ? 'failed' : 'completed',
            'records_synced' => $count,
            'error_message' => $error,
            'started_at' => now(),
            'completed_at' => now(),
        ]);
    }

    /**
     * Default ping implementation - override in specific adapters.
     */
    public function ping(): bool
    {
        return $this->isConfigured();
    }

    /**
     * Default sync implementation - can be overridden.
     */
    public function syncHotels(?string $destinationCode = null): int
    {
        // Default implementation - override in specific adapters
        return 0;
    }

    /**
     * Create a failed booking result.
     */
    protected function failedBookingResult(string $message, array $errors = []): BookingResult
    {
        return new BookingResult(
            supplierCode: $this->getSupplierCode(),
            status: BookingResult::STATUS_FAILED,
            message: $message,
            errors: $errors,
        );
    }

    /**
     * Create an unavailable availability result.
     */
    protected function unavailableResult(string $hotelId): HotelAvailabilityResult
    {
        return new HotelAvailabilityResult(
            supplierCode: $this->getSupplierCode(),
            supplierHotelId: $hotelId,
            isAvailable: false,
        );
    }
}
