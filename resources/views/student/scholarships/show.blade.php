<x-app-layout title="{{ $scholarship->name }} - CSU–Lal-lo">
    
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('student.scholarships.index') }}" class="text-xs font-bold text-csu-green hover:underline inline-flex items-center gap-1 mb-2">
                &larr; Back to Catalog
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">{{ $scholarship->name }}</h2>
                    <p class="text-xs text-slate-500 font-medium">Provider: <span class="font-bold text-slate-800">{{ $scholarship->provider }}</span> | School Year: <span class="font-bold text-slate-800">{{ $scholarship->school_year_label }}</span></p>
                </div>
                
                @if($existingApplication)
                    <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-xl text-xs font-extrabold border border-blue-300">
                        Application Already Submitted
                    </span>
                @elseif(!$isOpen)
                    <span class="px-4 py-2 bg-red-100 text-red-800 rounded-xl text-xs font-extrabold border border-red-300">
                        Application Closed
                    </span>
                @endif
            </div>
        </div>

        <!-- Program Overview Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6 sm:p-8 space-y-6">
            <div>
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">Description</h3>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $scholarship->description }}</p>
            </div>

            <div class="border-t border-slate-100 pt-4">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">Benefits & Coverage</h3>
                <p class="text-sm font-bold text-[#6B0F1A] bg-emerald-50 p-4 rounded-xl border border-emerald-100">{{ $scholarship->benefits }}</p>
            </div>

            <div class="border-t border-slate-100 pt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 font-semibold block">Available Slots</span>
                    <span class="font-extrabold {{ $scholarship->remaining_slots > 0 ? 'text-slate-900' : 'text-rose-600' }} text-sm">{{ $scholarship->remaining_slots }}/{{ $scholarship->available_slots }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Coverage Type</span>
                    <span class="font-extrabold text-[#3B060F]">{{ $scholarship->coverage_type_label }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Application Start</span>
                    <span class="font-extrabold text-slate-900">{{ $scholarship->application_start_date->format('M d, Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Deadline</span>
                    <span class="font-extrabold text-slate-900 text-red-600">{{ $scholarship->application_deadline->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Requirements Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Eligibility Checklist -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Eligibility Criteria
                </h3>

                <ul class="space-y-3">
                    @forelse($scholarship->requirements->where('requirement_type', 'eligibility') as $req)
                        <li class="flex items-start space-x-2 text-xs text-slate-700">
                            <span class="text-emerald-600 font-bold mt-0.5">&check;</span>
                            <span>{{ $req->requirement_name }}</span>
                        </li>
                    @empty
                        <li class="text-xs text-slate-400 italic">No specific eligibility rules specified.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Required Submission Documents -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Required Submission Documents
                </h3>

                <ul class="space-y-3">
                    @forelse($scholarship->requirements->where('requirement_type', 'document') as $req)
                        <li class="flex items-center justify-between text-xs text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <span class="font-medium">{{ $req->requirement_name }}</span>
                            @if($req->is_required)
                                <span class="text-[10px] bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded">Mandatory</span>
                            @endif
                        </li>
                    @empty
                        <li class="text-xs text-slate-400 italic">No document uploads required.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        @if(!$existingApplication && $isOpen)
            <div class="text-center pt-4">
                <a href="{{ route('student.applications.create', $scholarship->id) }}" class="px-8 py-3.5 bg-[#FFC107] text-[#3B060F] font-extrabold rounded-xl text-sm hover:bg-amber-400 transition shadow-lg inline-flex items-center gap-2">
                    Start Application & Upload Requirements &rarr;
                </a>
            </div>
        @endif
    </div>

</x-app-layout>
