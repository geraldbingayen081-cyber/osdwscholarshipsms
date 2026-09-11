<?php

namespace App\Notifications;

use App\Models\ScholarComplianceDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ComplianceDocumentVerifiedNotification extends Notification
{
    use Queueable;

    public ScholarComplianceDocument $document;

    public function __construct(ScholarComplianceDocument $document)
    {
        $this->document = $document;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->document->verification_status;
        $reqName = $this->document->requirement->name;
        $complianceRequest = $this->document->scholarCompliance->complianceRequest;

        if ($status === 'needs_correction') {
            $reason = $this->document->admin_remarks ? " Reason: {$this->document->admin_remarks}" : '';
            $message = "Your submission for '{$reqName}' under {$complianceRequest->formatted_period} needs correction.{$reason} Please upload the corrected document.";
            $title = 'Compliance Document Needs Correction';
        } elseif ($status === 'verified') {
            $message = "Your submission for '{$reqName}' under {$complianceRequest->formatted_period} has been verified successfully.";
            $title = 'Compliance Document Verified';
        } else {
            $reason = $this->document->admin_remarks ? " Reason: {$this->document->admin_remarks}" : '';
            $message = "Your submission for '{$reqName}' under {$complianceRequest->formatted_period} was rejected.{$reason}";
            $title = 'Compliance Document Rejected';
        }

        return [
            'type' => 'compliance_document_status',
            'compliance_request_id' => $complianceRequest->id,
            'title' => $title,
            'message' => $message,
            'url' => route('student.compliance.show', $complianceRequest->id),
        ];
    }
}
