<?php

namespace Modules\Faq\Http\Controllers;

use Illuminate\View\View;
use Modules\Faq\Models\Faq;
use Modules\Faq\Models\FaqCategory;
use Modules\Support\Http\Controllers\SiteController;

class SiteFaqController extends SiteController
{
    public function index(): View
    {
        $categories = FaqCategory::query()
            ->where('is_active', true)
            ->with(['faqs' => function ($query) {
                $query->where('status', 'published')
                    ->orderBy('sort_order')
                    ->orderBy('created_at', 'desc');
            }])
            ->orderBy('sort_order')
            ->get();

        $featuredFaqs = Faq::query()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit(5)
            ->get();

        return view('faq::index', compact('categories', 'featuredFaqs'));
    }
}
