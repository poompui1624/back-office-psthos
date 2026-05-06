<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SystemSettingController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('setting.view'), 403);

        $settings = SystemSetting::query()
            ->where('is_active', true)
            ->orderBy('group')
            ->orderBy('id')
            ->get()
            ->groupBy('group');

        return view('system-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()->can('setting.update'), 403);

        $validated = $request->validate([
            'settings' => ['nullable', 'array'],
            'setting_files' => ['nullable', 'array'],
            'setting_files.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $settings = $validated['settings'] ?? [];

        foreach ($settings as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();

            if (! $setting) {
                continue;
            }

            if ($setting->type === 'image') {
                continue;
            }

            $setting->update([
                'value' => $value,
            ]);
        }

        if ($request->hasFile('setting_files')) {
            foreach ($request->file('setting_files') as $key => $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }

                $setting = SystemSetting::where('key', $key)->first();

                if (! $setting) {
                    continue;
                }

                if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }

                $path = $file->store('logos', 'public');

                $setting->update([
                    'value' => $path,
                ]);
            }
        }

        Cache::forget('system_settings_all');

        return redirect()
            ->route('system-settings.index')
            ->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }
}
