<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Setting\Http\Requests\SettingValidate;
use Modules\Setting\Models\Setting;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\UploadFile;

class SettingController extends BackendController
{
    use UploadFile;

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

        // dd($request->all());
        $settingData = $request->validated();

        if ($request->hasFile('logo')) {
            $settingData = array_merge($settingData, $this->uploadFile('logo', 'site', 'originalUUID', 'public'));
        } elseif ($request->input('remove_previous_logo')) {
            $settingData['logo'] = null;
        } else {
            unset($settingData['logo']);
        }

        if ($request->hasFile('favicon')) {
            $settingData = array_merge($settingData, $this->uploadFile('favicon', 'site', 'originalUUID', 'public'));
        } elseif ($request->input('remove_previous_favicon')) {
            $settingData['favicon'] = null;
        } else {
            unset($settingData['favicon']);
        }

        //upload watermark
        if ($request->hasFile('watermark')) {
            $watermarkFile = $request->file('watermark');

            // Define path and filename
            $destinationPath = public_path(); // uploads to the public/ directory
            $fileName = 'watermark.png';

            // Move the file and overwrite existing watermark.png
            $watermarkFile->move($destinationPath, $fileName);

            // Optionally, you can store 'watermark.png' in DB if needed
            $settingData['watermark'] = $fileName;
        } elseif ($request->input('remove_previous_watermark')) {
            $watermarkPath = public_path('watermark.png');

            if (file_exists($watermarkPath)) {
                unlink($watermarkPath);
            }

            $settingData['watermark'] = null;
        } else {
            unset($settingData['watermark']);
        }


        $setting->update($settingData);

        return redirect()->route('setting.index')
            ->with('success', 'Setting updated.');
    }

    // public function update(SettingValidate $request): RedirectResponse
    // {
    //     $setting = Setting::firstOrCreate();
    //     $settingData = $request->validated();

    //     if ($request->hasFile('logo')) {
    //         $logoPath = $request->file('logo')->store('site', 'public');
    //         $settingData['logo'] = $logoPath;
    //     } elseif ($request->input('remove_previous_logo')) {
    //         $settingData['logo'] = null;
    //     } else {
    //         unset($settingData['logo']);
    //     }

    //     if ($request->hasFile('favicon')) {
    //         $faviconPath = $request->file('favicon')->store('site', 'public');
    //         $settingData['favicon'] = $faviconPath;
    //     } elseif ($request->input('remove_previous_favicon')) {
    //         $settingData['favicon'] = null;
    //     } else {
    //         unset($settingData['favicon']);
    //     }

    //     $setting->update($settingData);

    //     return redirect()->route('setting.index')->with('success', 'Setting updated.');
    // }

    public function maintenance(): Response
    {
        return inertia('Setting/AppMaintenance');
    }
}
