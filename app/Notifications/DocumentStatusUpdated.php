<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentStatusUpdated extends Notification
{
    use Queueable;

    public ApplicationDocument $document;
    public string $message;

    public function __construct(ApplicationDocument $document, string $status, ?string $remarks = null)
    {
        $this->document = $document;

        $docName = $document->requirement->requirement_name ?? 'Document';
        $scholarshipName = $document->application->scholarship->name ?? 'Scholarship';

        if ($status === 'needs_resubmission') {
            $this->message = "Document '{$docName}' for your '{$scholarshipName}' application requires resubmission." . ($remarks ? " Reason: {$remarks}" : '');
        } else {
            $this->message = "Document '{$docName}' for your '{$scholarshipName}' application has been verified.";
        }
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->document->application_id,
            'document_id' => $this->document->id,
            'document_name' => $this->document->requirement->requirement_name ?? '',
            'status' => $this->document->status,
            'remarks' => $this->document->remarks,
            'message' => $this->message,
            'url' => route('student.applications.show', $this->document->application_id),
        ];
    }
}
