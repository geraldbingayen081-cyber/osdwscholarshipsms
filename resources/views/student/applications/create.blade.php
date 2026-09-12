<x-app-layout title="Apply for {{ $scholarship->name }} - CSU–Lal-lo">
    
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('student.scholarships.show', $scholarship->id) }}" class="text-xs font-bold text-csu-green hover:underline inline-flex items-center gap-1 mb-2">
                &larr; Back to Scholarship Details
            </a>
            <h2 class="text-2xl font-extrabold text-slate-900">Scholarship Application Form</h2>
            <p class="text-xs text-slate-500">Submitting application for <span class="font-bold text-slate-800">{{ $scholarship->name }}</span> ({{ $scholarship->provider }}).</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6 sm:p-8">
            <form method="POST" action="{{ route('student.applications.store', $scholarship->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Student Summary Banner -->
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                    <div>
                        <p class="font-bold text-slate-900 text-sm">{{ Auth::user()->full_name }}</p>
                        <p class="text-slate-500">{{ $student->student_number }} • {{ $student->course }} ({{ $student->year_level }})</p>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 font-semibold block">Contact:</span>
                        <span class="font-bold text-slate-800">{{ $student->contact_number }}</span>
                    </div>
                </div>

                @php
                    $eligibilityReqs = $scholarship->requirements->where('requirement_type', 'eligibility');
                    $documentReqs = $scholarship->requirements->where('requirement_type', 'document');
                @endphp

                <!-- Eligibility Criteria Checklist (Informational / No file upload needed) -->
                @if($eligibilityReqs->isNotEmpty())
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-3">
                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Eligibility Criteria
                        </h3>
                        <ul class="space-y-2">
                            @foreach($eligibilityReqs as $eligReq)
                                <li class="flex items-start space-x-2 text-xs text-slate-700">
                                    <span class="text-emerald-600 font-bold mt-0.5">&check;</span>
                                    <span>{{ $eligReq->requirement_name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Document Upload Section (Only for Required Submission Documents) -->
                <div>
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Upload Required Application Documents
                    </h3>
                    <p class="text-xs text-slate-500 mb-6">Allowed formats: PDF, JPG, JPEG, PNG (Maximum file size: 5MB per file).</p>

                    <div class="space-y-6">
                        @forelse($documentReqs as $docReq)
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label for="doc_{{ $docReq->id }}" class="block text-xs font-bold text-slate-800 mb-1">
                                    {{ $docReq->requirement_name }}
                                    @if($docReq->is_required)
                                        <span class="text-red-500">*</span>
                                    @else
                                        <span class="text-slate-400 font-normal">(Optional)</span>
                                    @endif
                                </label>
                                <input 
                                    type="file" 
                                    name="doc_{{ $docReq->id }}" 
                                    id="doc_{{ $docReq->id }}" 
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    {{ $docReq->is_required ? 'required' : '' }}
                                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#6B0F1A] file:text-white hover:file:bg-[#500A15]"
                                >
                                @error("doc_{$docReq->id}")
                                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        @empty
                            <div class="p-4 text-center text-xs text-slate-400 italic">No specific documents required for this program. You may submit your application directly.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('student.scholarships.show', $scholarship->id) }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-[#6B0F1A] text-white font-extrabold rounded-xl text-xs hover:bg-[#500A15] transition shadow-md">
                        Submit Complete Application
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
