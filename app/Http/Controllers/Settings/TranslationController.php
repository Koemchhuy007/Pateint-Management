<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
    private string $enPath;
    private string $kmPath;

    public function __construct()
    {
        $this->enPath = resource_path('lang/en.json');
        $this->kmPath = resource_path('lang/km.json');
    }

    public function index()
    {
        $en = json_decode(File::get($this->enPath), true);
        $km = json_decode(File::get($this->kmPath), true);

        // Group keys by prefix (e.g. "nav", "common", "field", etc.)
        $groups = [];
        foreach ($en as $key => $enValue) {
            $prefix = explode('.', $key)[0];
            $groups[$prefix][$key] = [
                'en' => $enValue,
                'km' => $km[$key] ?? '',
            ];
        }

        return view('settings.translations.index', compact('groups'));
    }

    public function update(Request $request)
    {
        $en  = json_decode(File::get($this->enPath), true);
        $km  = json_decode(File::get($this->kmPath), true);

        $incoming = $request->input('km', []);

        foreach ($incoming as $key => $value) {
            // Only allow updating keys that exist in the master en.json
            if (array_key_exists($key, $en)) {
                $km[$key] = $value;
            }
        }

        File::put($this->kmPath, json_encode($km, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return redirect()->route('settings.translations.index')
            ->with('success', __('lang.saved'));
    }
}
