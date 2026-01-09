<?php

namespace Modules\Tour\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Models\TourDestination;
use Modules\Tour\Services\TourBookingService;

class SiteTourController extends SiteController
{
    public function __construct(
        protected TourBookingService $bookingService
    ) {}

    public function index(): View
    {
        $featuredTours = Tour::query()
            ->active()
            ->featured()
            ->with(['category', 'destination'])
            ->take(8)
            ->get();

        $categories = TourCategory::query()
            ->active()
            ->withCount('tours')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $destinations = TourDestination::query()
            ->active()
            ->featured()
            ->take(6)
            ->get();

        return view('tour::index', [
            'featuredTours' => $featuredTours,
            'categories' => $categories,
            'destinations' => $destinations,
        ]);
    }

    public function search(): View
    {
        $query = Tour::query()
            ->active()
            ->with(['category', 'destination']);

        // Filter by category
        if (request('category')) {
            $query->where('category_id', request('category'));
        }

        // Filter by destination
        if (request('destination')) {
            $query->where('destination_id', request('destination'));
        }

        // Filter by duration
        if (request('duration')) {
            $duration = request('duration');
            if ($duration === '1-3') {
                $query->whereBetween('duration_days', [1, 3]);
            } elseif ($duration === '4-7') {
                $query->whereBetween('duration_days', [4, 7]);
            } elseif ($duration === '7+') {
                $query->where('duration_days', '>', 7);
            }
        }

        // Search by name
        if (request('q')) {
            $query->where('name', 'like', '%'.request('q').'%');
        }

        // Sort
        $sort = request('sort', 'latest');
        if ($sort === 'price_low') {
            $query->orderByRaw('COALESCE(discount_price, base_price) ASC');
        } elseif ($sort === 'price_high') {
            $query->orderByRaw('COALESCE(discount_price, base_price) DESC');
        } elseif ($sort === 'popular') {
            $query->orderBy('view_count', 'desc');
        } else {
            $query->latest();
        }

        $tours = $query->paginate(12)->withQueryString();

        $categories = TourCategory::active()->orderBy('sort_order')->get();
        $destinations = TourDestination::active()->orderBy('name')->get();

        return view('tour::search-tour', [
            'tours' => $tours,
            'categories' => $categories,
            'destinations' => $destinations,
        ]);
    }

    public function show(string $slug): View
    {
        $tour = Tour::query()
            ->where('slug', $slug)
            ->active()
            ->with([
                'category',
                'destination',
                'itineraries' => fn ($q) => $q->orderBy('day_number'),
                'inclusions',
                'faqs',
                'highlights',
                'reviews' => fn ($q) => $q->approved()->latest()->take(5),
                'departureDates' => fn ($q) => $q->upcoming()->orderBy('departure_date'),
            ])
            ->firstOrFail();

        // Related tours
        $relatedTours = Tour::query()
            ->active()
            ->where('id', '!=', $tour->id)
            ->where(function ($q) use ($tour) {
                $q->where('category_id', $tour->category_id)
                    ->orWhere('destination_id', $tour->destination_id);
            })
            ->take(4)
            ->get();

        return view('tour::show-tour', [
            'tour' => $tour,
            'relatedTours' => $relatedTours,
        ]);
    }

    public function bookingPage(string $slug): View
    {
        $tour = Tour::query()
            ->where('slug', $slug)
            ->active()
            ->with([
                'departureDates' => fn ($q) => $q->upcoming()->orderBy('departure_date'),
                'extras',
            ])
            ->firstOrFail();

        return view('tour::booking', [
            'tour' => $tour,
        ]);
    }

    public function submitBooking(Request $request, string $slug): RedirectResponse
    {
        $tour = Tour::where('slug', $slug)->active()->firstOrFail();

        $validated = $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'departure_date_id' => 'nullable|exists:tour_departure_dates,id',
            'adults_count' => 'required|integer|min:1|max:50',
            'children_count' => 'nullable|integer|min:0|max:50',
            'infants_count' => 'nullable|integer|min:0|max:50',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            'special_requests' => 'nullable|string|max:2000',
        ]);

        // Add booking source and user_id
        $validated['booking_source'] = 'website';
        $validated['user_id'] = auth()->id();
        $validated['total_travelers'] = ($validated['adults_count'] ?? 1)
            + ($validated['children_count'] ?? 0)
            + ($validated['infants_count'] ?? 0);

        // Create booking
        $booking = $this->bookingService->create($validated);

        // Store booking ID in session for confirmation page
        session()->flash('booking_id', $booking->id);
        session()->flash('booking_number', $booking->booking_number);

        return redirect()->route('site.tour.bookingConfirmation', [
            'slug' => $slug,
            'booking' => $booking->booking_number,
        ])->with('success', 'Your booking has been submitted successfully!');
    }

    public function bookingConfirmation(string $slug, string $booking): View
    {
        $tour = Tour::where('slug', $slug)->firstOrFail();

        $bookingRecord = $this->bookingService->getAll()
            ->getCollection()
            ->firstWhere('booking_number', $booking);

        if (! $bookingRecord) {
            abort(404, 'Booking not found');
        }

        return view('tour::booking-confirmation', [
            'tour' => $tour,
            'booking' => $bookingRecord,
        ]);
    }

    public function submitReview(Request $request, string $slug): RedirectResponse
    {
        $tour = Tour::where('slug', $slug)->active()->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:2000',
            'reviewer_name' => 'required|string|max:255',
            'reviewer_email' => 'required|email|max:255',
            'travel_date' => 'nullable|date|before_or_equal:today',
            'pros' => 'nullable|array',
            'pros.*' => 'string|max:255',
            'cons' => 'nullable|array',
            'cons.*' => 'string|max:255',
        ]);

        // Create review (pending approval)
        $tour->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'reviewer_name' => $validated['reviewer_name'],
            'reviewer_email' => $validated['reviewer_email'],
            'travel_date' => $validated['travel_date'] ?? null,
            'pros' => $validated['pros'] ?? null,
            'cons' => $validated['cons'] ?? null,
            'is_approved' => false,
            'is_verified' => auth()->check(),
        ]);

        return redirect()
            ->route('site.tour.show', $slug)
            ->with('success', 'Thank you for your review! It will be published after approval.');
    }
}
