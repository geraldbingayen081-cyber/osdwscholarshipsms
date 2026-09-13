<?php

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('admin can access system settings page', function () {
    $admin = User::create([
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'email' => 'admin@csu.edu.ph',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.settings.index'));

    $response->assertStatus(200);
    $response->assertSee('System & Institutional Information', false);
    $response->assertSee('Admin Account Information', false);
});

test('non-admin student cannot access system settings and is redirected', function () {
    $studentUser = User::create([
        'first_name' => 'Juan',
        'last_name' => 'Cruz',
        'email' => 'student@csu.edu.ph',
        'password' => bcrypt('password123'),
        'role' => 'student',
    ]);

    $response = $this->actingAs($studentUser)->get(route('admin.settings.index'));

    $response->assertRedirect(route('student.dashboard'));
});

test('admin can update personal profile information and profile photo', function () {
    Storage::fake('public');

    $admin = User::create([
        'first_name' => 'Initial',
        'last_name' => 'Name',
        'email' => 'initial@csu.edu.ph',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);

    $avatar = UploadedFile::fake()->image('avatar.jpg', 200, 200)->size(1500); // 1.5MB

    $response = $this->actingAs($admin)->post(route('admin.settings.update-profile'), [
        'first_name' => 'UpdatedFirst',
        'middle_name' => 'Middle',
        'last_name' => 'UpdatedLast',
        'email' => 'updated@csu.edu.ph',
        'profile_photo' => $avatar,
    ]);

    $response->assertRedirect(route('admin.settings.index'));
    $response->assertSessionHas('success');

    $admin->refresh();
    expect($admin->first_name)->toBe('UpdatedFirst');
    expect($admin->last_name)->toBe('UpdatedLast');
    expect($admin->email)->toBe('updated@csu.edu.ph');
    expect($admin->profile_photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($admin->profile_photo_path);
});

test('admin can update password with correct current password', function () {
    $admin = User::create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin.pw@csu.edu.ph',
        'password' => bcrypt('oldpassword123'),
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.settings.update-profile'), [
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin.pw@csu.edu.ph',
        'current_password' => 'oldpassword123',
        'new_password' => 'NewSecurePassword123!',
        'new_password_confirmation' => 'NewSecurePassword123!',
    ]);

    $response->assertRedirect(route('admin.settings.index'));
    $admin->refresh();
    expect(Hash::check('NewSecurePassword123!', $admin->password))->toBeTrue();
});

test('admin can update system branding and upload custom logo up to 5MB', function () {
    Storage::fake('public');

    $admin = User::create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin.branding@csu.edu.ph',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);

    $logo = UploadedFile::fake()->image('custom_logo.png', 400, 400)->size(4000); // 4MB (<5MB limit)

    $response = $this->actingAs($admin)->post(route('admin.settings.update-system'), [
        'system_name' => 'Customized Scholarship Management System',
        'system_acronym' => 'CSU-LAL-LO-SMS',
        'institution_name' => 'Cagayan State University System',
        'campus_name' => 'Lal-lo Campus North',
        'office_name' => 'Office of Student Development',
        'contact_email' => 'helpdesk@csu.edu.ph',
        'contact_phone' => '0912-999-8888',
        'address' => 'Lal-lo Campus Address',
        'system_logo' => $logo,
    ]);

    $response->assertRedirect(route('admin.settings.index'));
    $response->assertSessionHas('success');

    expect(SystemSetting::get('system_name'))->toBe('Customized Scholarship Management System');
    expect(SystemSetting::get('institution_name'))->toBe('Cagayan State University System');
    expect(SystemSetting::get('campus_name'))->toBe('Lal-lo Campus North');
    expect(SystemSetting::logoUrl())->not->toBeNull();

    $logoPath = SystemSetting::get('system_logo_path');
    Storage::disk('public')->assertExists($logoPath);

    // Test resetting logo back to default
    $resetResponse = $this->actingAs($admin)->post(route('admin.settings.update-system'), [
        'system_name' => 'Customized Scholarship Management System',
        'institution_name' => 'Cagayan State University System',
        'campus_name' => 'Lal-lo Campus North',
        'office_name' => 'Office of Student Development',
        'reset_logo' => '1',
    ]);

    $resetResponse->assertRedirect(route('admin.settings.index'));
    expect(SystemSetting::get('system_logo_path'))->toBeNull();
    expect(SystemSetting::logoUrl())->toBeNull();
});

test('admin can upload and reset separate school logo', function () {
    Storage::fake('public');

    $admin = User::create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin.schoollogo@csu.edu.ph',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);

    $schoolLogo = UploadedFile::fake()->image('csu_school_seal.png', 400, 400)->size(3500); // 3.5MB

    $response = $this->actingAs($admin)->post(route('admin.settings.update-system'), [
        'system_name' => 'OSDW Scholarship Management System',
        'institution_name' => 'Cagayan State University',
        'campus_name' => 'Lal-lo Campus',
        'office_name' => 'Office of Student Development & Welfare',
        'school_logo' => $schoolLogo,
    ]);

    $response->assertRedirect(route('admin.settings.index'));
    $response->assertSessionHas('success');

    expect(SystemSetting::schoolLogoUrl())->not->toBeNull();
    $schoolLogoPath = SystemSetting::get('school_logo_path');
    Storage::disk('public')->assertExists($schoolLogoPath);

    // Verify school logo is displayed in guest banner
    $resGuest = $this->get(route('login'));
    $resGuest->assertSee(SystemSetting::schoolLogoUrl(), false);

    // Test resetting school logo back to default CSU seal
    $resetResponse = $this->actingAs($admin)->post(route('admin.settings.update-system'), [
        'system_name' => 'OSDW Scholarship Management System',
        'institution_name' => 'Cagayan State University',
        'campus_name' => 'Lal-lo Campus',
        'office_name' => 'Office of Student Development & Welfare',
        'reset_school_logo' => '1',
    ]);

    $resetResponse->assertRedirect(route('admin.settings.index'));
    expect(SystemSetting::get('school_logo_path'))->toBeNull();
    expect(SystemSetting::schoolLogoUrl())->toBeNull();
});

test('login page dynamically displays uploaded system logo in circular format', function () {
    Storage::fake('public');

    // Default seal when no logo is uploaded
    $resDefault = $this->get(route('login'));
    $resDefault->assertStatus(200);

    // Set custom logo
    SystemSetting::set('system_logo_path', 'system/custom_seal.png');
    
    $resCustom = $this->get(route('login'));
    $resCustom->assertStatus(200);
    $resCustom->assertSee(SystemSetting::logoUrl(), false);
});
