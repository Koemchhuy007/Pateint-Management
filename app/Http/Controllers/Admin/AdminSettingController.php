<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminSettingController extends Controller
{
    private const TIMEZONES = [
        'Asia/Phnom_Penh' => 'Asia/Phnom_Penh (ICT +07:00)',
        'Asia/Bangkok'    => 'Asia/Bangkok (ICT +07:00)',
        'Asia/Singapore'  => 'Asia/Singapore (SGT +08:00)',
        'Asia/Tokyo'      => 'Asia/Tokyo (JST +09:00)',
        'Asia/Seoul'      => 'Asia/Seoul (KST +09:00)',
        'UTC'             => 'UTC (+00:00)',
    ];

    public function index()
    {
        $settings  = SystemSetting::allKeyed();
        $timezones = self::TIMEZONES;

        return view('admin.settings.general', compact('settings', 'timezones'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name'        => ['required', 'string', 'max:100'],
            'app_description' => ['nullable', 'string', 'max:255'],
            'default_locale'  => ['required', Rule::in(['km', 'en'])],
            'app_timezone'    => ['required', Rule::in(array_keys(self::TIMEZONES))],
        ]);

        foreach ($data as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'System settings saved successfully.');
    }
}
