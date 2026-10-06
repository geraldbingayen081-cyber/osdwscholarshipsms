<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentStatusUpdated extends Notification
{
    use Queueable;

    public ApplicationDocument $document;
    public string $message;
    public string $status;

    public function __construct(ApplicationDocument $document, string $status, ?string $remarks = null)
    {
        $this->document = $document;
        $this->status = $status;

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
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $docName = $this->document->requirement->requirement_name ?? 'Document';
        $scholarshipName = $this->document->application->scholarship->name ?? 'Scholarship';
        $statusStr = $this->status === 'needs_resubmission' ? 'Resubmission Required' : 'Verified';

        $mail = (new MailMessage)
            ->subject("Document Status Notice: {$docName} ({$statusStr})")
            ->greeting("Hello {$notifiable->full_name},")
            ->line("Your submitted document **{$docName}** for the **{$scholarshipName}** scholarship application has been reviewed.")
            ->line("**Status:** {$statusStr}");

        if (!empty($this->document->remarks)) {
            $mail->line("**Remarks / Instructions:** {$this->document->remarks}");
        }

        $url = $this->status === 'needs_resubmission' 
            ? route('student.applications.resolve', $this->document->application_id)
            : route('student.applications.show', $this->document->application_id);

        return $mail->action('Review / Resubmit Document', $url)
            ->line('Please take action promptly if resubmission is required.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'document_status',
            'application_id' => $this->document->application_id,
            'document_id' => $this->document->id,
            'document_name' => $this->document->requirement->requirement_name ?? '',
            'status' => $this->status,
            'remarks' => $this->document->remarks,
            'message' => $this->message,
            'url' => route('student.applications.show', $this->document->application_id),
            'action_label' => $this->status === 'needs_resubmission' ? 'Resubmit Document' : 'View Document',
        ];
    }
}
