<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Setting\Http\Requests\SettingValidate;
use Modules\Setting\Models\Setting;
use Modules\Support\Http\Controllers\BackendController;

class SettingController extends BackendController
{
    public function index(): Response
    {
        $settings = Setting::firstOrCreate();

        return inertia('Setting/SettingIndex', [
            'settings' => $settings,
        ]);
    }

    public function update(SettingValidate $request): RedirectResponse
    {
        $setting = Setting::firstOrCreate();

        $settingData = $request->validated();

        // Remove file-related keys from settingData as they're handled via media library
        unset($settingData['logo'], $settingData['favicon'], $settingData['banner']);
        unset($settingData['remove_previous_logo'], $settingData['remove_previous_favicon'], $settingData['remove_previous_banner']);

        // Handle logo upload via media library
        if ($request->hasFile('logo')) {
            $setting->clearMediaCollection('logo');
            $setting->addMediaFromRequest('logo')->toMediaCollection('logo');
        } elseif ($request->boolean('remove_previous_logo')) {
            $setting->clearMediaCollection('logo');
        }

        // Handle favicon upload via media library
        if ($request->hasFile('favicon')) {
            $setting->clearMediaCollection('favicon');
            $setting->addMediaFromRequest('favicon')->toMediaCollection('favicon');
        } elseif ($request->boolean('remove_previous_favicon')) {
            $setting->clearMediaCollection('favicon');
        }

        // Handle banner upload via media library
        if ($request->hasFile('banner')) {
            $setting->clearMediaCollection('banner');
            $setting->addMediaFromRequest('banner')->toMediaCollection('banner');
        } elseif ($request->boolean('remove_previous_banner')) {
            $setting->clearMediaCollection('banner');
        }

        // Handle watermark (stays in public directory)
        if ($request->hasFile('watermark')) {
            $watermarkFile = $request->file('watermark');
            $watermarkFile->move(public_path(), 'watermark.png');
        } elseif ($request->boolean('remove_previous_watermark')) {
            $watermarkPath = public_path('watermark.png');
            if (file_exists($watermarkPath)) {
                unlink($watermarkPath);
            }
        }
        unset($settingData['watermark'], $settingData['remove_previous_watermark']);

        $setting->update($settingData);

        return redirect()->route('setting.index')
            ->with('success', 'Setting updated.');
    }

    public function maintenance(): Response
    {
        return inertia('Setting/AppMaintenance');
    }
}
