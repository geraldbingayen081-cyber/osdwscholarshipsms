<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdated extends Notification
{
    use Queueable;

    public Application $application;
    public string $message;

    public function __construct(Application $application, ?string $customMessage = null)
    {
        $this->application = $application;

        $scholarshipName = $application->scholarship->name ?? 'Scholarship';
        $statusVal = $application->status instanceof \App\Enums\ApplicationStatus ? $application->status->value : (string) $application->status;
        $statusStr = ucfirst(str_replace('_', ' ', $statusVal));

        if ($customMessage) {
            $this->message = $customMessage;
        } else {
            $this->message = "Your application for '{$scholarshipName}' has been updated to: {$statusStr}.";
        }
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusVal = $this->application->status instanceof \App\Enums\ApplicationStatus ? $this->application->status->value : (string) $this->application->status;
        return [
            'application_id' => $this->application->id,
            'scholarship_id' => $this->application->scholarship_id,
            'scholarship_name' => $this->application->scholarship->name ?? '',
            'status' => $statusVal,
            'remarks' => $this->application->remarks,
            'message' => $this->message,
            'url' => route('student.applications.show', $this->application->id),
        ];
    }
}
