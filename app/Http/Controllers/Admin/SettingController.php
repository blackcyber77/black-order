<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'Kantin Industri Batang'),
            'service_fee' => Setting::get('service_fee', 2000),
            'qris_image' => Setting::get('qris_image'),
            'store_address' => Setting::get('store_address'),
            'store_phone' => Setting::get('store_phone'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'service_fee' => 'required|integer|min:0',
            'store_address' => 'nullable|string|max:500',
            'store_phone' => 'nullable|string|max:20',
            'qris_image' => 'nullable|image|max:2048',
        ]);

        Setting::set('store_name', $request->store_name);
        Setting::set('service_fee', $request->service_fee);
        Setting::set('store_address', $request->store_address);
        Setting::set('store_phone', $request->store_phone);

        if ($request->hasFile('qris_image')) {
            // Delete old image
            $oldImage = Setting::get('qris_image');
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
            
            $path = $request->file('qris_image')->store('qris', 'public');
            Setting::set('qris_image', $path);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan');
    }
}
