<?php

namespace Modules\Testimonial\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\UploadFile;
use Modules\Testimonial\Http\Requests\TestimonialValidate;
use Modules\Testimonial\Models\Testimonial;

class TestimonialController extends BackendController
{
    use UploadFile;

    public function index(): Response
    {
        $testimonials = Testimonial::latest()
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn($testimonial) => [
                'id' => $testimonial->id,
                'name' => $testimonial->name,
                'designation' => $testimonial->designation,
                'company' => $testimonial->company,
                'title' => $testimonial->title,
                'description' => $testimonial->description,
                'image_url' => $testimonial->image_url,
                'active' => $testimonial->active,
                'featured' => $testimonial->featured,
            ]);

        return inertia('Testimonial/TestimonialIndex', [
            'testimonials' => $testimonials,
        ]);
    }

    public function create(): Response
    {
        return inertia('Testimonial/TestimonialForm');
    }

    public function store(TestimonialValidate $request): RedirectResponse
    {
        $validated = $request->validated();

        // if ($request->hasFile('image')) {
        //     $validated = array_merge($validated, $this->uploadFile('image', 'testimonial', 'originalUUID', 'public'));
        // }

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');

            // Store original temporarily
            $originalPath = $imageFile->store('temp_images', 'public');
            $watermarkedPath = storage_path('app/public/temp_watermarked_' . $imageFile->hashName());

            // Apply watermark
            \Spatie\Image\Image::load(storage_path("app/public/{$originalPath}"))
                ->watermark(public_path('watermark.png'))
                // ->watermarkPosition('bottom-right')
                // ->watermarkPadding(10, 10)
                ->save($watermarkedPath);

            // Move watermarked file to final destination
            $finalPath = 'testimonial/' . $imageFile->hashName();
            \Illuminate\Support\Facades\Storage::disk('public')->put("{$finalPath}", file_get_contents($watermarkedPath));

            // Save path in validated (adjust field if needed)
            $validated['image'] = $imageFile->hashName();

            // Cleanup temp files
            unlink(storage_path("app/public/{$originalPath}"));
            unlink($watermarkedPath);
        }


        Testimonial::create($validated);

        return redirect()->route('testimonial.index')
            ->with('success', 'Testimonial created.');
    }

    public function edit(int $id): Response
    {
        $testimonial = Testimonial::find($id);

        return inertia('Testimonial/TestimonialForm', [
            'testimonial' => $testimonial,
        ]);
    }

    public function update(TestimonialValidate $request, int $id): RedirectResponse
    {
        $testimonial = Testimonial::findOrFail($id);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // $validated = array_merge($validated, $this->uploadFile('image', 'testimonial', 'originalUUID', 'public'));
            $imageFile = $request->file('image');

            // Store original temporarily
            $originalPath = $imageFile->store('temp_images', 'public');
            $watermarkedPath = storage_path('app/public/temp_watermarked_' . $imageFile->hashName());

            // Apply watermark
            \Spatie\Image\Image::load(storage_path("app/public/{$originalPath}"))
                ->watermark(public_path('watermark.png'))
                // ->watermarkPosition('bottom-right')
                // ->watermarkPadding(10, 10)
                ->save($watermarkedPath);

            // Move watermarked file to final destination
            $finalPath = 'testimonial/' . $imageFile->hashName();
            \Illuminate\Support\Facades\Storage::disk('public')->put("{$finalPath}", file_get_contents($watermarkedPath));

            // Save path in validated (adjust field if needed)
            $validated['image'] = $imageFile->hashName();

            // Cleanup temp files
            unlink(storage_path("app/public/{$originalPath}"));
            unlink($watermarkedPath);
        } elseif ($request->input('remove_previous_image')) {
            $validated['image'] = null;
        } else {
            unset($validated['image']);
        }

        $testimonial->update($validated);

        return redirect()->route('testimonial.index')
            ->with('success', 'Testimonial updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Testimonial::findOrFail($id)->delete();

        return redirect()->route('testimonial.index')
            ->with('success', 'Testimonial deleted.');
    }

    public function recycleBin(): Response
    {
        $testimonials = Testimonial::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn($testimonial) => [
                'id' => $testimonial->id,
                'name' => $testimonial->name,
                'designation' => $testimonial->designation,
                'company' => $testimonial->company,
                'deleted_at' => $testimonial->deleted_at ? Carbon::parse($testimonial->deleted_at)->format('d/m/Y') : null,
                'deletedBy' => $testimonial->deletedBy,
                'active' => $testimonial->active,
            ]);

        return inertia('Testimonial/TestimonialRecycleBin', [
            'testimonials' => $testimonials,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Testimonial::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('testimonial.recycleBin.index')
            ->with('success', 'Testimonial Restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $testimonial = Testimonial::onlyTrashed()->findOrFail($id);

        $testimonial->forceDelete();

        return redirect()->route('testimonial.recycleBin.index')->with('success', 'Testimonial deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $testimonials = Testimonial::onlyTrashed()->get();

        foreach ($testimonials as $testimonial) {
            $testimonial->forceDelete();
        }

        return redirect()->route('testimonial.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Testimonial::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('testimonial.recycleBin.index')
            ->with('success', 'Testimonial Restored.');
    }
}
