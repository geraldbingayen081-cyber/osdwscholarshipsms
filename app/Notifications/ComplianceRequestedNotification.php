<?php

namespace App\Notifications;

use App\Models\ComplianceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ComplianceRequestedNotification extends Notification
{
    use Queueable;

    public ComplianceRequest $complianceRequest;

    public function __construct(ComplianceRequest $complianceRequest)
    {
        $this->complianceRequest = $complianceRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $reqCount = $this->complianceRequest->requirements->count();
        $deadlineStr = $this->complianceRequest->deadline->format('M d, Y');

        return [
            'type' => 'compliance_requested',
            'compliance_request_id' => $this->complianceRequest->id,
            'scholarship_id' => $this->complianceRequest->scholarship_id,
            'title' => 'Compliance Required',
            'message' => "Compliance documents requested for '{$this->complianceRequest->scholarship->name}' ({$this->complianceRequest->formatted_period}). Please submit {$reqCount} required document(s) on or before {$deadlineStr}.",
            'url' => route('student.compliance.show', $this->complianceRequest->id),
        ];
    }
}
