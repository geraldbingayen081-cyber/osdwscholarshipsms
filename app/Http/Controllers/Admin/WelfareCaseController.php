<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\WelfareCase;
use App\Models\WelfareCaseDocument;
use App\Models\WelfareCaseReferral;
use App\Notifications\WelfareCaseReferralCreated;
use App\Notifications\WelfareCaseStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WelfareCaseController extends Controller
{
    /**
     * Display listing of all student welfare cases for OSDW Admin.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $category = $request->get('category');
        $search = $request->get('search');

        $query = WelfareCase::with(['student.user', 'documents', 'referrals.scholarship']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('case_id', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                  })
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('student_number', 'like', "%{$search}%")
                         ->orWhere('course', 'like', "%{$search}%");
                  });
            });
        }

        $welfareCases = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total' => WelfareCase::count(),
            'open' => WelfareCase::where('status', 'Open')->count(),
            'under_assessment' => WelfareCase::where('status', 'Under Assessment')->count(),
            'referred' => WelfareCase::where('status', 'Referred')->count(),
            'for_followup' => WelfareCase::where('status', 'For Follow-up')->count(),
            'resolved' => WelfareCase::where('status', 'Resolved')->count(),
            'closed' => WelfareCase::where('status', 'Closed')->count(),
        ];

        return view('admin.welfare_cases.index', compact('welfareCases', 'stats', 'status', 'category', 'search'));
    }

    /**
     * Display a specific welfare case details, student academic profile, and history.
     */
    public function show(WelfareCase $welfareCase)
    {
        $welfareCase->load([
            'student.user',
            'student.scholars.scholarship',
            'student.applications.scholarship',
            'documents',
            'referrals.scholarship'
        ]);

        $availableScholarships = Scholarship::where('status', 'open')->orWhere('status', 'active')->get();

        return view('admin.welfare_cases.show', compact('welfareCase', 'availableScholarships'));
    }

    /**
     * Update welfare case status and admin staff notes.
     */
    public function updateStatus(Request $request, WelfareCase $welfareCase)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Open,Under Assessment,Referred,For Follow-up,Resolved,Closed',
            'requested_information' => 'nullable|string|max:1500',
        ]);

        $oldStatus = $welfareCase->status;

        $welfareCase->update([
            'status' => $validated['status'],
            'requested_information' => $validated['requested_information'] ?? null,
        ]);

        \App\Models\SystemLog::record(
            'Welfare Case',
            'status_change',
            "Updated Welfare Case #{$welfareCase->case_id} ({$welfareCase->category}) status to {$validated['status']}.",
            $welfareCase,
            [
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'requested_information' => $validated['requested_information'] ?? null,
            ]
        );

        return redirect()->route('admin.welfare-cases.show', $welfareCase->id)
            ->with('success', "Welfare case #{$welfareCase->case_id} status updated to {$welfareCase->status}.");
    }

    /**
     * Refer student welfare case to an official scholarship program or university office.
     */
    public function refer(Request $request, WelfareCase $welfareCase)
    {
        $validated = $request->validate([
            'referral_type' => 'required|in:scholarship,office',
            'scholarship_id' => 'required_if:referral_type,scholarship|nullable|exists:scholarships,id',
            'referred_to_office' => 'required_if:referral_type,office|nullable|string|max:255',
            'referral_note' => 'required|string|max:1500',
        ]);

        $referral = null;

        DB::transaction(function () use ($validated, $welfareCase, &$referral) {
            $referral = $welfareCase->referrals()->create([
                'referral_type' => $validated['referral_type'],
                'scholarship_id' => $validated['referral_type'] === 'scholarship' ? $validated['scholarship_id'] : null,
                'referred_to_office' => $validated['referral_type'] === 'office' ? $validated['referred_to_office'] : null,
                'referral_note' => $validated['referral_note'],
            ]);

            $welfareCase->update(['status' => 'Referred']);
        });

        if ($referral && $welfareCase->student && $welfareCase->student->user) {
            $welfareCase->student->user->notify(new WelfareCaseReferralCreated($referral));
        }

        $targetName = $referral ? $referral->recipient_name : 'the designated office';

        \App\Models\SystemLog::record(
            'Welfare Case',
            'referral',
            "Officially referred Welfare Case #{$welfareCase->case_id} to {$targetName}.",
            $welfareCase,
            [
                'referral_type' => $validated['referral_type'],
                'target' => $targetName,
                'notes' => $validated['referral_note'],
            ]
        );

        return redirect()->route('admin.welfare-cases.show', $welfareCase->id)
            ->with('success', "Welfare case #{$welfareCase->case_id} has been referred to {$targetName} and notification sent to student.");
    }

    /**
     * Preview an uploaded welfare case document directly in browser (PDF, images, etc.).
     */
    public function viewDocument(WelfareCaseDocument $document)
    {
        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'The requested document file was not found.');
        }

        $path = Storage::disk('local')->path($document->file_path);
        
        $extension = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => Storage::disk('local')->mimeType($document->file_path) ?? 'application/pdf',
        };

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($document->file_name) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Securely download submitted welfare case document.
     */
    public function downloadDocument(WelfareCaseDocument $document)
    {
        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Document file not found.');
        }

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }
}
