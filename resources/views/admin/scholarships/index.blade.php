<x-app-layout title="Scholarships - CSU–Lal-lo">
    <x-slot name="header">
        Scholarship Management
    </x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800">Scholarship Programs Catalog</h3>
            <p class="text-xs text-slate-500">Manage institutional, LGU, and government scholarship offerings.</p>
        </div>
        <a href="{{ route('admin.scholarships.create') }}" class="px-4 py-2 bg-csu-green text-white font-bold rounded-lg text-xs hover:bg-csu-green-dark transition shadow-sm inline-flex items-center gap-1.5 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Scholarship
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="mb-6 bg-white rounded-2xl shadow-xs border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.scholarships.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search Input -->
            <div class="sm:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Search scholarship name or provider..." 
                       {{ $search ? 'autofocus onfocus="this.setSelectionRange(this.value.length, this.value.length)"' : '' }}
                       oninput="clearTimeout(window._searchTimer); window._searchTimer = setTimeout(() => this.form.submit(), 400)"
                       class="w-full py-2 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
            </div>

            <!-- Academic School Year Filter -->
            <div class="sm:col-span-3">
                <select name="school_year" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                    <option value="">-- All School Years --</option>
                    @foreach($availableSchoolYears as $sy)
                        @php
                            $clean = str_replace('AY ', '', $sy);
                        @endphp
                        <option value="{{ $clean }}" {{ (isset($schoolYear) && ($schoolYear == $clean || $schoolYear == "AY {$clean}" || $schoolYear == $sy)) ? 'selected' : '' }}>
                            AY {{ $clean }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-3 flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                    <option value="">-- All Statuses --</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="archived" {{ $status === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                @if($search || $schoolYear || $status)
                    <a href="{{ route('admin.scholarships.index') }}" class="px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition shrink-0" title="Reset Filters">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Scholarships Table -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Scholarship Program</th>
                        <th class="px-6 py-3.5">Provider</th>
                        <th class="px-6 py-3.5">AY / Semester</th>
                        <th class="px-6 py-3.5">Slots</th>
                        <th class="px-6 py-3.5">Applications</th>
                        <th class="px-6 py-3.5">Deadline</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($scholarships as $s)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.scholarships.show', $s->id) }}" class="font-bold text-slate-800 hover:text-csu-green hover:underline">
                                    {{ $s->name }}
                                </a>
                                <p class="text-xs text-slate-500 line-clamp-1">{{ $s->description }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                {{ $s->provider }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <span class="font-bold text-slate-800">{{ $s->school_year_label }}</span>
                                <p class="text-[11px] font-semibold {{ $s->isContinuing() ? 'text-emerald-700' : 'text-blue-700' }}">
                                    {{ $s->coverage_type_label }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-800">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold {{ $s->remaining_slots > 0 ? 'bg-slate-100 text-slate-800 border border-slate-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                    {{ $s->remaining_slots }}/{{ $s->available_slots }} Available
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-700 font-medium">
                                {{ $s->applications_count }} Submitted
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $s->application_deadline->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize 
                                    @if($s->status === 'open') bg-emerald-100 text-emerald-800 border border-emerald-300
                                    @elseif($s->status === 'closed') bg-red-100 text-red-800 border border-red-300
                                    @elseif($s->status === 'archived') bg-slate-200 text-slate-700 border border-slate-300
                                    @else bg-amber-100 text-amber-800 border border-amber-300 @endif">
                                    {{ $s->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5 items-stretch min-w-[85px] max-w-[110px] ml-auto">
                                    <a href="{{ route('admin.scholarships.show', $s->id) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-extrabold bg-[#3B060F] text-white rounded-xl hover:bg-[#6B0F1A] transition shadow-2xs text-center">
                                        <svg class="w-3.5 h-3.5 text-[#FFC107] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                    <a href="{{ route('admin.scholarships.edit', $s->id) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-extrabold bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition shadow-2xs text-center">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.scholarships.destroy', $s->id) }}" onsubmit="return confirm('Are you sure you want to delete this scholarship? All related requirements, applications, and scholars will also be permanently deleted.');" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-extrabold bg-rose-600 text-white rounded-xl hover:bg-rose-700 transition shadow-2xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-500">
                                No scholarship programs found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $scholarships->links() }}
        </div>
    </div>
</x-app-layout>
