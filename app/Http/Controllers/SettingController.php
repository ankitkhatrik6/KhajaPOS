<?php

namespace App\Http\Controllers;

use App\Models\RestaurantSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = RestaurantSetting::getAllSettings();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'restaurant_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
            'pan_number' => ['nullable', 'string', 'max:50'],
            'vat_number' => ['nullable', 'string', 'max:50'],
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'invoice_footer' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $val) {
            RestaurantSetting::set($key, $val);
        }

        return back()->with('success', 'Restaurant settings saved successfully.');
    }
}
