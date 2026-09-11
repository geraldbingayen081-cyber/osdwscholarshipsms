<x-app-layout title="My Compliance Requests - CSU Lal-lo">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">My Periodic Compliance Requests</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Periodic requirements requested by OSDW for your active scholarship programs (e.g. COE, ID, Grades).
            </p>
        </div>

        <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
            &larr; Back to Dashboard
        </a>
    </div>

    <!-- Compliances List -->
    <div class="space-y-4">
        @forelse($compliances as $comp)
            @php
                $cr = $comp->complianceRequest;
                $totalReqs = $cr->requirements->count();
                $verifiedCount = $comp->documents->where('verification_status', 'verified')->count();
                $submittedCount = $comp->documents->count();
            @endphp
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5 hover:border-slate-300 transition flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div class="flex items-start space-x-4">
                    <div class="h-12 w-12 rounded-2xl flex items-center justify-center font-bold text-xl shrink-0 shadow-xs
                        @if($comp->status === 'needs_correction') bg-amber-100 text-amber-900 border border-amber-300
                        @elseif($comp->status === 'completed') bg-emerald-100 text-emerald-800 border border-emerald-300
                        @elseif($comp->status === 'overdue') bg-rose-100 text-rose-900 border border-rose-300
                        @else bg-[#3B060F] text-[#FFC107] @endif">
                        📋
                    </div>

                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                @if($comp->status === 'completed') bg-emerald-100 text-emerald-800 border border-emerald-300
                                @elseif($comp->status === 'needs_correction') bg-amber-200 text-amber-900 border border-amber-400 font-black animate-pulse
                                @elseif($comp->status === 'overdue') bg-rose-100 text-rose-800 border border-rose-300
                                @elseif($comp->status === 'under_review' || $comp->status === 'submitted') bg-indigo-100 text-indigo-800 border border-indigo-200
                                @elseif($comp->status === 'partially_submitted') bg-blue-100 text-blue-800 border border-blue-200
                                @else bg-slate-100 text-slate-800 border border-slate-300 @endif">
                                {{ str_replace('_', ' ', $comp->status) }}
                            </span>
                            <span class="text-xs font-bold text-slate-500">
                                {{ $cr->semester }} AY {{ $cr->school_year }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="text-xs font-bold text-[#3B060F]">
                                {{ $cr->scholarship->name }}
                            </span>
                        </div>

                        <h3 class="text-base font-extrabold text-slate-900 mt-1.5">
                            {{ $cr->title }}
                        </h3>

                        <div class="flex flex-wrap items-center gap-4 mt-2 text-xs text-slate-500">
                            <div>
                                Deadline: <strong class="{{ $cr->isPastDeadline() ? 'text-red-600' : 'text-slate-700' }}">{{ $cr->deadline->format('M d, Y') }}</strong>
                                @if(!$cr->isPastDeadline())
                                    <span class="text-slate-400">({{ $cr->days_remaining }} left)</span>
                                @else
                                    <span class="text-red-600 font-bold">(Past Deadline)</span>
                                @endif
                            </div>
                            <div>
                                Requirements: <strong>{{ $submittedCount }} / {{ $totalReqs }} Submitted</strong>
                                ({{ $verifiedCount }} Verified)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('student.compliance.show', $cr->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 font-extrabold text-xs rounded-xl transition shadow-sm
                        @if($comp->status === 'needs_correction') bg-amber-600 text-white hover:bg-amber-700
                        @elseif($comp->status === 'completed') bg-slate-100 text-slate-700 hover:bg-slate-200
                        @else bg-[#3B060F] text-white hover:bg-[#6B0F1A] border border-[#FFC107]/40 @endif">
                        @if($comp->status === 'needs_correction')
                            ⚠️ Resubmit Required Files
                        @elseif($comp->status === 'completed')
                            View Submitted Files
                        @else
                            Open Compliance Checklist &rarr;
                        @endif
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-12 text-center text-slate-500">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <h3 class="text-sm font-extrabold text-slate-800">No Compliance Requests</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">You currently do not have any pending compliance requests from OSDW. When requested, you will see your submission checklist here.</p>
            </div>
        @endforelse

        @if($compliances->hasPages())
            <div class="pt-4">
                {{ $compliances->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
