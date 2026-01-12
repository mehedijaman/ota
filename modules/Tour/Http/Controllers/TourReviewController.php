<?php

namespace Modules\Tour\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Tour\Models\TourReview;
use Modules\Tour\Services\TourReviewService;

class TourReviewController extends BackendController
{
    public function __construct(
        protected TourReviewService $reviewService
    ) {}

    public function index(): Response
    {
        return inertia('TourReview/ReviewIndex', [
            'reviews' => $this->reviewService->getAll(),
        ]);
    }

    public function show(int $id): Response
    {
        return inertia('TourReview/ReviewShow', [
            'review' => $this->reviewService->getById($id),
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $review = TourReview::findOrFail($id);

        $this->reviewService->approve($review);

        return redirect()->back()
            ->with('success', 'Review approved successfully.');
    }

    public function reject(int $id): RedirectResponse
    {
        $review = TourReview::findOrFail($id);

        $this->reviewService->reject($review);

        return redirect()->back()
            ->with('success', 'Review rejected.');
    }

    public function toggleFeatured(int $id): RedirectResponse
    {
        $review = TourReview::findOrFail($id);

        $this->reviewService->toggleFeatured($review);

        return redirect()->back()
            ->with('success', 'Review featured status updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $review = TourReview::findOrFail($id);

        $this->reviewService->delete($review);

        return redirect()->route('tourReview.index')
            ->with('success', 'Review deleted successfully.');
    }

    public function recycleBin(): Response
    {
        return inertia('TourReview/ReviewRecycleBin', [
            'reviews' => $this->reviewService->getTrashed(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $review = TourReview::onlyTrashed()->findOrFail($id);

        $this->reviewService->restore($review);

        return redirect()->back()
            ->with('success', 'Review restored successfully.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->reviewService->restoreAll();

        return redirect()->back()
            ->with('success', 'All reviews restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $review = TourReview::onlyTrashed()->findOrFail($id);

        $this->reviewService->forceDelete($review);

        return redirect()->back()
            ->with('success', 'Review permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->reviewService->emptyRecycleBin();

        return redirect()->back()
            ->with('success', 'Recycle bin emptied successfully.');
    }
}
