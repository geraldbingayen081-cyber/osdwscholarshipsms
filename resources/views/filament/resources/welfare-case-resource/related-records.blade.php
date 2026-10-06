@php
    $student = $getRecord()->student;
    $applications = $student->applications()->with('scholarship')->latest()->get();
    $activeScholar = $student->scholars()->where('status', 'active')->with('scholarship')->first();
    $pastCases = $student->welfareCases()->where('id', '!=', $getRecord()->id)->latest()->get();
    $documents = $getRecord()->documents;
@endphp

<div class="space-y-6">
    <!-- Documents Section -->
    @if($documents->isNotEmpty())
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Supporting Documents</h3>
            <ul class="space-y-2">
                @foreach($documents as $doc)
                    <li class="flex justify-between items-center bg-slate-50 p-2 rounded-lg border border-slate-100">
                        <span class="text-xs font-semibold text-slate-700">{{ $doc->file_name }}</span>
                        <a href="{{ route('admin.welfare-cases.documents.view', $doc->id) }}" target="_blank" class="text-xs text-blue-600 font-semibold hover:underline">View Document</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Active Grant Section -->
    @if($activeScholar)
        <div class="bg-emerald-50 p-4 rounded-xl shadow-sm border border-emerald-200">
            <h3 class="text-sm font-bold text-emerald-900 mb-2">Active Scholarship Grant</h3>
            <p class="text-xs text-emerald-800">
                <strong>Program:</strong> {{ $activeScholar->scholarship->name }} <br>
                <strong>Status:</strong> {{ ucfirst($activeScholar->status) }}
            </p>
            <p class="text-[10px] text-emerald-700 mt-2 font-semibold">Note: Student already holds an active grant. Recommend other non-scholarship interventions.</p>
        </div>
    @endif

    <!-- Past Scholarship Applications Section -->
    @if($applications->isNotEmpty())
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Scholarship Applications History</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-500 uppercase">
                            <th class="pb-2">Program</th>
                            <th class="pb-2">Date</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($applications as $app)
                            <tr>
                                <td class="py-2 font-medium text-slate-700">{{ $app->scholarship->name }}</td>
                                <td class="py-2 text-slate-500">{{ $app->created_at->format('M d, Y') }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] bg-slate-100 border border-slate-200">{{ ucfirst(str_replace('_', ' ', $app->status)) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Past Welfare Cases Section -->
    @if($pastCases->isNotEmpty())
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Past Welfare Cases</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-500 uppercase">
                            <th class="pb-2">Case ID</th>
                            <th class="pb-2">Category</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pastCases as $pastCase)
                            <tr>
                                <td class="py-2 font-medium text-slate-700">{{ $pastCase->case_id }}</td>
                                <td class="py-2 text-slate-500">{{ $pastCase->category }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] bg-slate-100 border border-slate-200">{{ $pastCase->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
