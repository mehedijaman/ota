<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Sms\Http\Requests\SmsTemplateRequest;
use Modules\Sms\Models\SmsTemplate;
use Modules\Support\Http\Controllers\BackendController;

class SmsTemplateController extends BackendController
{
    public function index(): Response
    {
        $searchTerm = request('searchTerm');

        $templates = SmsTemplate::query()
            ->when($searchTerm, fn ($q) => $q->where('name', 'like', "%{$searchTerm}%"))
            ->latest('id')
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn (SmsTemplate $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'content' => $t->content,
                'category' => $t->category,
                'is_active' => (bool) $t->is_active,
                'created_at' => $t->created_at?->format('M d, Y'),
            ]);

        return inertia('Sms/Template/SmsTemplateIndex', [
            'templates' => $templates,
        ]);
    }

    public function create(): Response
    {
        return inertia('Sms/Template/SmsTemplateForm', [
            'categories' => $this->getCategoryOptions(),
        ]);
    }

    public function store(SmsTemplateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        SmsTemplate::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'variables' => $this->extractVariables($validated['content']),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('sms.templates.index')
            ->with('success', 'SMS Template created successfully.');
    }

    public function edit(SmsTemplate $smsTemplate): Response
    {
        return inertia('Sms/Template/SmsTemplateForm', [
            'template' => [
                'id' => $smsTemplate->id,
                'name' => $smsTemplate->name,
                'slug' => $smsTemplate->slug,
                'content' => $smsTemplate->content,
                'category' => $smsTemplate->category,
                'is_active' => (bool) $smsTemplate->is_active,
            ],
            'categories' => $this->getCategoryOptions(),
        ]);
    }

    public function update(SmsTemplateRequest $request, SmsTemplate $smsTemplate): RedirectResponse
    {
        $validated = $request->validated();

        $smsTemplate->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'variables' => $this->extractVariables($validated['content']),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('sms.templates.index')
            ->with('success', 'SMS Template updated successfully.');
    }

    public function destroy(SmsTemplate $smsTemplate): RedirectResponse
    {
        $smsTemplate->delete();

        return redirect()->route('sms.templates.index')
            ->with('success', 'SMS Template deleted successfully.');
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function getCategoryOptions(): array
    {
        return [
            ['value' => 'promotional', 'label' => 'Promotional'],
            ['value' => 'transactional', 'label' => 'Transactional'],
            ['value' => 'reminder', 'label' => 'Reminder'],
            ['value' => 'notification', 'label' => 'Notification'],
            ['value' => 'other', 'label' => 'Other'],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function extractVariables(string $content): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/', $content, $matches);

        return array_unique($matches[1] ?? []);
    }
}
