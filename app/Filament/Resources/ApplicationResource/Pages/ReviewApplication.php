<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Scholar;
use App\Notifications\ApplicationStatusUpdated;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ReviewApplication extends Page
{
    protected static string $resource = ApplicationResource::class;

    protected string $view = 'filament.resources.application-resource.pages.review-application';

    public Application $record;

    public ?ApplicationDocument $selectedDoc = null;

    public string $rejectionRemarks = '';

    public function mount(Application $record): void
    {
        $this->record = $record->load([
            'student.user',
            'scholarship.academicYear',
            'scholarship.requirements',
            'documents.requirement',
        ]);

        if ($this->record->documents->count() > 0) {
            $this->selectedDoc = $this->record->documents->first();
        }
    }

    public function selectDoc(int $docId): void
    {
        $this->selectedDoc = $this->record->documents->firstWhere('id', $docId);
    }

    public function verifyDoc(int $docId): void
    {
        $doc = ApplicationDocument::find($docId);
        if ($doc) {
            $doc->update([
                'status' => 'verified',
                'verification_status' => 'verified',
                'verified_at' => now(),
            ]);

            $this->record->load('documents');
            $this->selectedDoc = $this->record->documents->firstWhere('id', $docId);

            Notification::make()
                ->title('Document verified')
                ->success()
                ->send();
        }
    }

    public function rejectDoc(int $docId): void
    {
        $doc = ApplicationDocument::find($docId);
        if ($doc) {
            $doc->update([
                'status' => 'needs_resubmission',
                'verification_status' => 'rejected',
                'remarks' => $this->rejectionRemarks ?: 'Document copy requires resubmission or clearer upload.',
            ]);

            $this->record->update(['status' => 'deficient']);
            $this->rejectionRemarks = '';
            $this->record->load('documents');
            $this->selectedDoc = $this->record->documents->firstWhere('id', $docId);

            Notification::make()
                ->title('Document flagged as deficient')
                ->warning()
                ->send();
        }
    }

    public function markDeficient(): void
    {
        $this->record->update([
            'status' => 'deficient',
            'rejection_reason' => $this->rejectionRemarks ?: 'Deficient document requirements detected during evaluation.',
        ]);

        if ($this->record->student && $this->record->student->user) {
            $this->record->student->user->notify(new ApplicationStatusUpdated($this->record, 'Your scholarship application requires document resubmission. Please review deficient documents on your portal.'));
        }

        Notification::make()
            ->title('Application returned to student for document resubmission')
            ->warning()
            ->send();

        $this->redirect(ApplicationResource::getUrl('index'));
    }

    public function endorseQualified(): void
    {
        $this->record->update([
            'status' => 'approved',
        ]);

        Scholar::firstOrCreate([
            'student_id' => $this->record->student_id,
            'scholarship_id' => $this->record->scholarship_id,
        ], [
            'application_id' => $this->record->id,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        if ($this->record->student && $this->record->student->user) {
            $this->record->student->user->notify(new ApplicationStatusUpdated($this->record, 'Congratulations! Your scholarship application has been approved. You are now enrolled as an Active CSU Scholar.'));
        }

        Notification::make()
            ->title('Application endorsed and student enrolled into Active Scholars!')
            ->success()
            ->send();

        $this->redirect(ApplicationResource::getUrl('index'));
    }
}
