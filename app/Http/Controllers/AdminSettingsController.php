<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function edit(): View
    {
        $this->ensureAdmin();

        return view('admin.settings.edit', [
            'guidanceTarget' => Setting::value('guidance_target_default', '16'),
        ]);
    }

    public function update(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'guidance_target_default' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        Setting::query()->updateOrCreate(
            ['key' => 'guidance_target_default'],
            ['value' => (string) $validated['guidance_target_default']],
        );

        return redirect()->route('admin.settings')->with('status', 'Target bimbingan default berhasil diperbarui.');
    }

    private function ensureAdmin(): void
    {
        abort_if(Auth::user()->role !== 'admin', 403);
    }
}
