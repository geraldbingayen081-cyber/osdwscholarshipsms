<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    /**
     * Display the System Settings and Admin Profile management interface.
     */
    public function index()
    {
        $admin = Auth::user();
        $settings = SystemSetting::allSettings();

        return view('admin.settings.index', compact('admin', 'settings'));
    }

    /**
     * Update Administrator Profile information, profile avatar, and credentials.
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'profile_photo.max' => 'The profile picture must not be greater than 5MB.',
            'current_password.current_password' => 'The provided current password does not match your current password.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
        ]);

        $updateData = [
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
        ];

        // Handle Profile Photo Removal
        if ($request->boolean('remove_photo')) {
            if ($admin->profile_photo_path && Storage::disk('public')->exists($admin->profile_photo_path)) {
                Storage::disk('public')->delete($admin->profile_photo_path);
            }
            $updateData['profile_photo_path'] = null;
        }

        // Handle New Profile Photo Upload
        if ($request->hasFile('profile_photo')) {
            if ($admin->profile_photo_path && Storage::disk('public')->exists($admin->profile_photo_path)) {
                Storage::disk('public')->delete($admin->profile_photo_path);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $updateData['profile_photo_path'] = $path;
        }

        // Handle Password Update
        if (!empty($validated['new_password'])) {
            $updateData['password'] = Hash::make($validated['new_password']);
        }

        $admin->update($updateData);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Admin profile and account details updated successfully.');
    }

    /**
     * Update System Information, Institutional Branding, and System Logo.
     */
    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'system_name' => ['required', 'string', 'max:255'],
            'system_acronym' => ['nullable', 'string', 'max:50'],
            'institution_name' => ['required', 'string', 'max:255'],
            'campus_name' => ['required', 'string', 'max:255'],
            'office_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'system_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'], // Max 5MB per user specification
        ], [
            'system_logo.max' => 'The system logo must not be greater than 5MB.',
        ]);

        // Save textual settings
        SystemSetting::set('system_name', $validated['system_name']);
        SystemSetting::set('system_acronym', $validated['system_acronym'] ?? 'OSDW-SMS');
        SystemSetting::set('institution_name', $validated['institution_name']);
        SystemSetting::set('campus_name', $validated['campus_name']);
        SystemSetting::set('office_name', $validated['office_name']);
        SystemSetting::set('contact_email', $validated['contact_email'] ?? '');
        SystemSetting::set('contact_phone', $validated['contact_phone'] ?? '');
        SystemSetting::set('address', $validated['address'] ?? '');

        // Handle Logo Reset to Default
        if ($request->boolean('reset_logo')) {
            $existingPath = SystemSetting::get('system_logo_path');
            if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                Storage::disk('public')->delete($existingPath);
            }
            SystemSetting::set('system_logo_path', null);
        }

        // Handle Custom Logo Upload
        if ($request->hasFile('system_logo')) {
            $existingPath = SystemSetting::get('system_logo_path');
            if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                Storage::disk('public')->delete($existingPath);
            }

            $path = $request->file('system_logo')->store('system', 'public');
            SystemSetting::set('system_logo_path', $path);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'System branding and institutional settings updated successfully.');
    }
}
