<x-app-layout title="System Settings - CSU Lal-lo Admin">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#6B0F1A] transition">&larr; Dashboard</a>
                <span>/</span>
                <span class="text-slate-800">Settings</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <div class="h-9 w-9 rounded-xl bg-[#3B060F] text-[#FFC107] flex items-center justify-center shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                System & Admin Settings
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Configure institutional system details, upload official logos, and manage administrator credentials.
            </p>
        </div>
    </div>


    @if($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold px-4 py-3 rounded-2xl shadow-xs">
            <div class="font-bold flex items-center gap-1.5 mb-1">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Please correct the following errors:
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Settings Grid Layout (2 Column split for System & Admin info) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- LEFT COLUMN: System Information & Official Logo (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6 sm:p-7">
                <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-[#3B060F]"></span>
                            System & Institutional Information
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Branding, header titles, official office name, and system logo.
                        </p>
                    </div>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-[10px] font-extrabold rounded-lg uppercase tracking-wider">
                        Public Branding
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.settings.update-system') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- System Logo Uploader with Live Preview -->
                    <div x-data="{ 
                            logoPreview: '{{ \App\Models\SystemSetting::logoUrl() }}',
                            resetFlag: false,
                            fileChosen(event) {
                                const file = event.target.files[0];
                                if (!file) return;
                                const reader = new FileReader();
                                reader.onload = (e) => { this.logoPreview = e.target.result; this.resetFlag = false; };
                                reader.readAsDataURL(file);
                            },
                            triggerReset() {
                                this.logoPreview = null;
                                this.resetFlag = true;
                                if ($refs.logoInput) $refs.logoInput.value = '';
                            }
                         }" 
                         class="bg-slate-50/80 p-5 rounded-2xl border border-slate-200 space-y-3">
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs font-bold text-slate-800">
                                    Official System Logo
                                </label>
                                <p class="text-[11px] text-slate-500">
                                    Displayed in sidebar navigation, headers, and reports (Max 5MB • PNG, JPG, WEBP, SVG).
                                </p>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">
                                Max 5MB
                            </span>
                        </div>

                        <input type="hidden" name="reset_logo" :value="resetFlag ? '1' : '0'">

                        <div class="flex flex-col sm:flex-row sm:items-center gap-5 pt-2">
                            <!-- Logo Preview Display Box (Circular) -->
                            <div class="h-20 w-20 rounded-full bg-[#3B060F] border-2 border-[#FFC107] flex items-center justify-center p-1.5 shrink-0 shadow-md relative overflow-hidden group">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" alt="System Logo" class="h-full w-full rounded-full object-cover">
                                </template>
                                <template x-if="!logoPreview">
                                    <x-csu-logo class="h-full w-full rounded-full" />
                                </template>
                            </div>

                            <!-- Upload Controls -->
                            <div class="space-y-2 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <label class="cursor-pointer px-3.5 py-2 bg-[#3B060F] text-white hover:bg-[#6B0F1A] font-extrabold text-xs rounded-xl transition shadow-xs inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <span>Upload New Logo</span>
                                        <input type="file" 
                                               name="system_logo" 
                                               x-ref="logoInput" 
                                               @change="fileChosen" 
                                               accept="image/png,image/jpeg,image/webp,image/svg+xml" 
                                               class="hidden">
                                    </label>

                                    <button type="button" 
                                            @click="triggerReset" 
                                            class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                                        Use Default Seal
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-400">
                                    Recommended: Transparent PNG or high-resolution vector format for best clarity on dark backgrounds.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- System & Campus Names -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">
                                System Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="system_name" 
                                   value="{{ old('system_name', $settings['system_name'] ?? 'OSDW Scholarship Management System') }}" 
                                   required 
                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">
                                System Acronym
                            </label>
                            <input type="text" 
                                   name="system_acronym" 
                                   value="{{ old('system_acronym', $settings['system_acronym'] ?? 'OSDW-SMS') }}" 
                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">
                                Campus Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="campus_name" 
                                   value="{{ old('campus_name', $settings['campus_name'] ?? 'Lal-lo Campus') }}" 
                                   required 
                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">
                                Parent Institution Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="institution_name" 
                                   value="{{ old('institution_name', $settings['institution_name'] ?? 'Cagayan State University') }}" 
                                   required 
                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">
                                Managing Office / Department <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="office_name" 
                                   value="{{ old('office_name', $settings['office_name'] ?? 'Office of Student Development & Welfare (OSDW)') }}" 
                                   required 
                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">
                                Official Contact Email
                            </label>
                            <input type="email" 
                                   name="contact_email" 
                                   value="{{ old('contact_email', $settings['contact_email'] ?? 'osdw.lallo@csu.edu.ph') }}" 
                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">
                                Official Contact Phone
                            </label>
                            <input type="text" 
                                   name="contact_phone" 
                                   value="{{ old('contact_phone', $settings['contact_phone'] ?? '(078) 123-4567') }}" 
                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">
                                Campus Address / Location
                            </label>
                            <textarea name="address" 
                                      rows="2" 
                                      class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">{{ old('address', $settings['address'] ?? 'Sta. Maria, Lal-lo, Cagayan 3509') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-[#3B060F] text-white hover:bg-[#6B0F1A] font-extrabold text-xs rounded-xl transition shadow-sm inline-flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Save System Information
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- RIGHT COLUMN: Admin Account & Profile Settings (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Admin Profile Form Card -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6 sm:p-7">
                <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-[#FFC107]"></span>
                            Admin Account Information
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Manage your administrator name, email, and avatar.
                        </p>
                    </div>
                    <span class="px-2.5 py-1 bg-amber-50 text-amber-900 text-[10px] font-extrabold rounded-lg uppercase tracking-wider border border-amber-200">
                        Profile
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.settings.update-profile') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Admin Profile Avatar with Live Preview -->
                    <div x-data="{ 
                            photoPreview: '{{ $admin->profile_photo_url }}',
                            removePhoto: false,
                            fileChosen(event) {
                                const file = event.target.files[0];
                                if (!file) return;
                                const reader = new FileReader();
                                reader.onload = (e) => { this.photoPreview = e.target.result; this.removePhoto = false; };
                                reader.readAsDataURL(file);
                            },
                            triggerRemove() {
                                this.photoPreview = null;
                                this.removePhoto = true;
                                if ($refs.photoInput) $refs.photoInput.value = '';
                            }
                         }" 
                         class="flex items-center gap-4 bg-slate-50/80 p-4 rounded-2xl border border-slate-200">
                        
                        <input type="hidden" name="remove_photo" :value="removePhoto ? '1' : '0'">

                        <div class="h-16 w-16 rounded-full bg-[#3B060F] text-[#FFC107] font-extrabold flex items-center justify-center text-lg border-2 border-[#6B0F1A] shadow-xs shrink-0 overflow-hidden">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" alt="Profile Photo" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!photoPreview">
                                <span>{{ strtoupper(substr($admin->first_name, 0, 1) . substr($admin->last_name, 0, 1)) }}</span>
                            </template>
                        </div>

                        <div class="space-y-1.5 flex-1">
                            <label class="block text-xs font-bold text-slate-800">Admin Photo</label>
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer px-3 py-1.5 bg-[#6B0F1A] hover:bg-[#500A15] text-white font-bold text-[11px] rounded-xl transition shadow-xs inline-block">
                                    Change Photo
                                    <input type="file" 
                                           name="profile_photo" 
                                           x-ref="photoInput" 
                                           @change="fileChosen" 
                                           accept="image/png,image/jpeg,image/webp" 
                                           class="hidden">
                                </label>

                                <button type="button" 
                                        @click="triggerRemove" 
                                        class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] rounded-xl transition cursor-pointer border border-rose-200">
                                    Remove
                                </button>
                            </div>
                            <span class="text-[10px] text-slate-400 block">Max 5MB • JPG, PNG, WEBP</span>
                        </div>
                    </div>

                    <!-- Personal Information Fields -->
                    <div class="space-y-3.5 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    First Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       name="first_name" 
                                       value="{{ old('first_name', $admin->first_name) }}" 
                                       required 
                                       class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    Last Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       name="last_name" 
                                       value="{{ old('last_name', $admin->last_name) }}" 
                                       required 
                                       class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">
                                Middle Name
                            </label>
                            <input type="text" 
                                   name="middle_name" 
                                   value="{{ old('middle_name', $admin->middle_name) }}" 
                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email', $admin->email) }}" 
                                   required 
                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>
                    </div>

                    <!-- Change Password Sub-section -->
                    <div class="pt-4 border-t border-slate-100 space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-800 text-xs">Security & Password Change</span>
                            <span class="text-[10px] text-slate-400">Leave blank to keep unchanged</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">
                                Current Password
                            </label>
                            <x-password-input 
                                name="current_password" 
                                id="admin_current_password"
                                autocomplete="current-password"
                                placeholder="Enter current password if changing" 
                                class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none"
                            />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    New Password
                                </label>
                                <x-password-input 
                                    name="new_password" 
                                    id="admin_new_password"
                                    autocomplete="new-password"
                                    placeholder="New password" 
                                    class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none"
                                />
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    Confirm New Password
                                </label>
                                <x-password-input 
                                    name="new_password_confirmation" 
                                    id="admin_new_password_confirmation"
                                    autocomplete="new-password"
                                    placeholder="Confirm password" 
                                    class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-[#3B060F] text-white hover:bg-[#6B0F1A] font-extrabold text-xs rounded-xl transition shadow-sm inline-flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Admin Profile
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</x-app-layout>
