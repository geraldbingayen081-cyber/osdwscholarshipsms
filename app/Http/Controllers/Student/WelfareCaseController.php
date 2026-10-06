<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\WelfareCase;
use App\Models\WelfareCaseDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WelfareCaseController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $welfareCases = WelfareCase::where('student_id', $student->id)->latest()->paginate(10);
        return view('student.welfare_cases.index', compact('welfareCases'));
    }

    public function create()
    {
        return view('student.welfare_cases.create');
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;

        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $createdCase = null;

        DB::transaction(function () use ($request, $student, &$createdCase) {
            $createdCase = WelfareCase::create([
                'case_id' => 'WC-' . strtoupper(Str::random(8)),
                'student_id' => $student->id,
                'category' => $request->category,
                'description' => $request->description,
                'status' => 'Open',
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $path = $file->store("private/welfare_cases/{$createdCase->id}", 'local');

                    WelfareCaseDocument::create([
                        'welfare_case_id' => $createdCase->id,
                        'file_path' => $path,
                        'file_name' => $originalName,
                    ]);
                }
            }
        });

        if ($createdCase) {
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\WelfareCaseSubmitted($createdCase));
            }

            \App\Models\SystemLog::record(
                'Welfare Case',
                'create',
                "Student " . (Auth::user()->full_name) . " reported a new Welfare Case #{$createdCase->case_id} ({$createdCase->category}).",
                $createdCase,
                [
                    'case_id' => $createdCase->case_id,
                    'category' => $createdCase->category,
                    'documents_count' => $request->hasFile('documents') ? count($request->file('documents')) : 0,
                ],
                Auth::user()
            );
        }

        return redirect()->route('student.welfare-cases.index')
            ->with('success', 'Welfare case concern submitted successfully.');
    }

    public function show(WelfareCase $welfareCase)
    {
        $student = Auth::user()->student;

        if ($welfareCase->student_id !== $student->id) {
            abort(403, 'Unauthorized access to welfare case.');
        }

        $welfareCase->load(['documents', 'referrals.scholarship']);

        return view('student.welfare_cases.show', compact('welfareCase'));
    }

    /**
     * Preview an uploaded welfare case document.
     */
    public function viewDocument(WelfareCaseDocument $document)
    {
        $student = Auth::user()->student;

        if ($document->welfareCase->student_id !== $student->id) {
            abort(403, 'Unauthorized access to document.');
        }

        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Document file not found.');
        }

        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($document->file_path);
        $extension = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => \Illuminate\Support\Facades\Storage::disk('local')->mimeType($document->file_path) ?? 'application/pdf',
        };

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($document->file_name) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
