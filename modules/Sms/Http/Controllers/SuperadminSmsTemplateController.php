<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Sms\Models\SmsTemplate;
use Modules\Support\Http\Controllers\BackendController;

class SuperadminSmsTemplateController extends BackendController
{
    public function index(): Response
    {
        $templates = SmsTemplate::query()
            ->latest('id')
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'content' => $t->content,
                'is_active' => (bool) $t->is_active,
                'created_at' => $t->created_at,
            ]);

        return inertia('Sms/Superadmin/TemplatesIndex', [
            'templates' => $templates,
        ]);
    }

    public function create(): Response
    {
        return inertia('Sms/Superadmin/TemplateForm');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sms_templates,name',
            'content' => 'required|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        SmsTemplate::create([
            ...$validated,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()->route('superadmin.sms.templates.index')->with('success', 'Template created.');
    }

    public function edit(SmsTemplate $smsTemplate): Response
    {
        return inertia('Sms/Superadmin/TemplateForm', [
            'template' => [
                'id' => $smsTemplate->id,
                'name' => $smsTemplate->name,
                'content' => $smsTemplate->content,
                'is_active' => (bool) $smsTemplate->is_active,
            ],
        ]);
    }

    public function update(Request $request, SmsTemplate $smsTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $smsTemplate->update([
            ...$validated,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('superadmin.sms.templates.index')->with('success', 'Template updated.');
    }

    public function destroy(SmsTemplate $smsTemplate): RedirectResponse
    {
        $smsTemplate->delete();

        return redirect()->route('superadmin.sms.templates.index')->with('success', 'Template deleted.');
    }
}
