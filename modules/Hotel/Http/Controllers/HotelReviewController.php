<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Hotel\Models\HotelReview;
use Modules\Hotel\Services\GetHotelOptions;
use Modules\Hotel\Services\HotelReviewService;
use Modules\Support\Http\Controllers\BackendController;

class HotelReviewController extends BackendController
{
    public function __construct(
        protected HotelReviewService $reviewService,
        protected GetHotelOptions $hotelOptions
    ) {}

    public function index(): Response
    {
        return inertia('HotelReview/ReviewIndex', [
            'reviews' => $this->reviewService->getAll(),
            'stats' => $this->reviewService->getStats(),
            'hotels' => $this->hotelOptions->get(),
        ]);
    }

    public function show(int $id): Response
    {
        return inertia('HotelReview/ReviewShow', [
            'review' => $this->reviewService->getById($id),
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $review = HotelReview::findOrFail($id);

        $this->reviewService->approve($review);

        return redirect()->back()
            ->with('success', 'Review approved and published.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $review = HotelReview::findOrFail($id);

        $this->reviewService->reject($review, $request->reason);

        return redirect()->back()
            ->with('success', 'Review rejected.');
    }

    public function toggleFeatured(int $id): RedirectResponse
    {
        $review = HotelReview::findOrFail($id);

        $this->reviewService->toggleFeatured($review);

        return redirect()->back()
            ->with('success', 'Review featured status updated.');
    }

    public function addResponse(Request $request, int $id): RedirectResponse
    {
        $request->validate(['response' => 'required|string|max:1000']);

        $review = HotelReview::findOrFail($id);

        $this->reviewService->addHotelResponse($review, $request->response);

        return redirect()->back()
            ->with('success', 'Hotel response added.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $review = HotelReview::findOrFail($id);

        $this->reviewService->delete($review);

        return redirect()->route('hotelReview.index')
            ->with('success', 'Review moved to recycle bin.');
    }

    public function recycleBin(): Response
    {
        return inertia('HotelReview/ReviewRecycleBin', [
            'reviews' => $this->reviewService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->reviewService->restore($id);

        return redirect()->route('hotelReview.recycleBin')
            ->with('success', 'Review restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->reviewService->forceDelete($id);

        return redirect()->route('hotelReview.recycleBin')
            ->with('success', 'Review permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->reviewService->emptyRecycleBin();

        return redirect()->route('hotelReview.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->reviewService->restoreAll();

        return redirect()->route('hotelReview.recycleBin')
            ->with('success', 'All reviews restored.');
    }
}
