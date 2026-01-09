<?php

namespace Modules\Tour\Services;

use Illuminate\Support\Facades\DB;
use Modules\Tour\Models\TourReview;

class TourReviewService
{
    public function getAll()
    {
        return TourReview::query()
            ->with(['tour', 'user'])
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($review) => [
                'id' => $review->id,
                'tour' => $review->tour?->name,
                'user' => $review->user?->name,
                'reviewer_name' => $review->reviewer_name,
                'rating' => $review->rating,
                'title' => $review->title,
                'is_verified' => $review->is_verified,
                'is_approved' => $review->is_approved,
                'is_featured' => $review->is_featured,
                'created_at' => $review->created_at->format('d M Y'),
            ]);
    }

    public function getById(int $id): TourReview
    {
        return TourReview::with(['tour', 'user'])->findOrFail($id);
    }

    public function create(array $data): TourReview
    {
        return DB::transaction(function () use ($data) {
            return TourReview::create($data);
        });
    }

    public function update(TourReview $review, array $data): TourReview
    {
        return DB::transaction(function () use ($review, $data) {
            $review->update($data);

            return $review;
        });
    }

    public function approve(TourReview $review): TourReview
    {
        $review->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        return $review;
    }

    public function reject(TourReview $review): TourReview
    {
        $review->update([
            'is_approved' => false,
            'approved_at' => null,
        ]);

        return $review;
    }

    public function toggleFeatured(TourReview $review): TourReview
    {
        $review->update([
            'is_featured' => ! $review->is_featured,
        ]);

        return $review;
    }

    public function delete(TourReview $review): bool
    {
        return $review->delete();
    }
}
