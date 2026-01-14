<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Hotel\Models\HotelReview;

class HotelReviewService
{
    public function getAll(): LengthAwarePaginator
    {
        return HotelReview::query()
            ->with(['hotel', 'customer', 'booking'])
            ->when(request('search'), fn ($q, $search) => $q->search($search))
            ->when(request('hotel_id'), fn ($q, $id) => $q->where('hotel_id', $id))
            ->when(request('status') === 'pending', fn ($q) => $q->pendingModeration())
            ->when(request('status') === 'approved', fn ($q) => $q->approved())
            ->when(request('status') === 'rejected', fn ($q) => $q->where('moderation_status', 'rejected'))
            ->when(request('rating'), fn ($q, $rating) => $q->where('overall_rating', $rating))
            ->orderBy(request('sort_by', 'created_at'), request('sort_order', 'desc'))
            ->paginate(request('per_page', 15))
            ->withQueryString();
    }

    public function getStats(): array
    {
        return [
            'total' => HotelReview::count(),
            'pending' => HotelReview::pendingModeration()->count(),
            'approved' => HotelReview::approved()->count(),
            'featured' => HotelReview::featured()->count(),
            'avg_rating' => round(HotelReview::published()->avg('overall_rating'), 2),
        ];
    }

    public function getById(int $id): HotelReview
    {
        return HotelReview::with(['hotel', 'customer', 'booking', 'moderatedBy', 'hotelRespondedBy'])
            ->findOrFail($id);
    }

    public function approve(HotelReview $review): void
    {
        $review->approve();
    }

    public function reject(HotelReview $review, string $reason): void
    {
        $review->reject($reason);
    }

    public function toggleFeatured(HotelReview $review): void
    {
        $review->update(['is_featured' => ! $review->is_featured]);
    }

    public function addHotelResponse(HotelReview $review, string $response): void
    {
        $review->addHotelResponse($response);
    }

    public function delete(HotelReview $review): void
    {
        $review->delete();
    }

    public function getRecycleBin(): LengthAwarePaginator
    {
        return HotelReview::onlyTrashed()
            ->with(['hotel', 'customer'])
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();
    }

    public function restore(int $id): void
    {
        HotelReview::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        HotelReview::onlyTrashed()->findOrFail($id)->forceDelete();
    }

    public function emptyRecycleBin(): void
    {
        HotelReview::onlyTrashed()->forceDelete();
    }

    public function restoreAll(): void
    {
        HotelReview::onlyTrashed()->restore();
    }
}
