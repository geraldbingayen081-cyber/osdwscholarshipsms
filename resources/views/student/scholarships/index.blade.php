<x-app-layout title="Available Scholarships - CSU–Lal-lo">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Available Scholarships</h2>
            <p class="text-xs text-slate-500">Explore open scholarship programs for Cagayan State University – Lal-lo students.</p>
        </div>
        <!-- Dynamic Search Input -->
        <div class="w-full sm:w-80">
            <form method="GET" action="{{ route('student.scholarships.index') }}" class="relative flex items-center">
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
                       class="w-full py-2 pl-9 pr-8 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none shadow-2xs">
                @if($search)
                    <a href="{{ route('student.scholarships.index') }}" class="absolute right-2.5 text-slate-400 hover:text-slate-700" title="Clear Search">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </form>
        </div>
    </div>


    @if(!empty($hasActiveScholarship) && !empty($activeScholar))
        <div class="mb-6 bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800/60 rounded-2xl p-4 sm:p-5 flex items-start gap-3 shadow-xs">
            <div class="h-9 w-9 rounded-xl bg-amber-200 text-amber-900 font-extrabold flex items-center justify-center shrink-0">
                ⚠️
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-amber-900 dark:text-amber-200 uppercase tracking-wider">
                    Active Scholarship Policy Notice
                </h4>
                <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5 leading-relaxed">
                    You are currently an active scholar under <strong>{{ $activeScholar->scholarship->name }}</strong>. In accordance with university scholarship guidelines, students who currently hold an active scholarship grant are not eligible to apply for other scholarship programs.
                </p>
            </div>
        </div>
    @endif

    <!-- Scholarships Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse($scholarships as $s)
            @php
                $isApplied = in_array($s->id, $appliedScholarshipIds);
            @endphp
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                <div class="p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $s->isContinuing() ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                            {{ $s->coverage_type_label }} • {{ $s->school_year_label }}
                        </span>
                        @if($isApplied)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-blue-100 text-blue-800 border border-blue-200">
                                Already Applied
                            </span>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 leading-tight">
                            <a href="{{ route('student.scholarships.show', $s->id) }}" class="hover:text-csu-green transition">
                                {{ $s->name }}
                            </a>
                        </h3>
                        <p class="text-xs font-semibold text-csu-gold-dark mt-1">Provider: {{ $s->provider }}</p>
                    </div>

                    <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                        {{ $s->description }}
                    </p>

                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 space-y-1 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Available Slots:</span>
                            <span class="font-extrabold {{ $s->remaining_slots > 0 ? 'text-slate-800' : 'text-rose-600' }}">{{ $s->remaining_slots }}/{{ $s->available_slots }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Deadline:</span>
                            <span class="font-bold text-slate-800">{{ $s->application_deadline->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Requirements: {{ $s->requirements->count() }}</span>
                    <a href="{{ route('student.scholarships.show', $s->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#6B0F1A] text-white text-xs font-extrabold rounded-xl hover:bg-[#500A15] transition shadow-xs">
                        <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ !empty($hasActiveScholarship) ? 'View Details' : 'View & Apply' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center text-slate-500 border border-slate-200">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                </svg>
                <h4 class="text-sm font-bold text-slate-700">No Open Scholarships</h4>
                <p class="text-xs text-slate-400 mt-1">There are currently no active scholarship programs open for application matching your query.</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $scholarships->links() }}
    </div>

</x-app-layout>
