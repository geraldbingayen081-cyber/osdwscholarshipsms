<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $scholarshipName = $this->application->scholarship->name ?? 'Scholarship';
        $statusVal = $this->application->status instanceof \App\Enums\ApplicationStatus ? $this->application->status->value : (string) $this->application->status;
        $statusStr = ucfirst(str_replace('_', ' ', $statusVal));

        $mail = (new MailMessage)
            ->subject("Application Status Update: {$scholarshipName} - {$statusStr}")
            ->greeting("Hello {$notifiable->full_name},")
            ->line("The status of your scholarship application for **{$scholarshipName}** has been updated.")
            ->line("**Status:** {$statusStr}");

        if (!empty($this->application->remarks)) {
            $mail->line("**Remarks / Instructions:** {$this->application->remarks}");
        }

        return $mail->action('View Application Status', route('student.applications.show', $this->application->id))
            ->line('Thank you for using the CSU-Lal-lo OSDW Scholarship Management System.');
    }

    public function toArray(object $notifiable): array
    {
        $statusVal = $this->application->status instanceof \App\Enums\ApplicationStatus ? $this->application->status->value : (string) $this->application->status;
        return [
            'type' => 'scholarship_application',
            'application_id' => $this->application->id,
            'scholarship_id' => $this->application->scholarship_id,
            'scholarship_name' => $this->application->scholarship->name ?? '',
            'status' => $statusVal,
            'remarks' => $this->application->remarks,
            'message' => $this->message,
            'url' => route('student.applications.show', $this->application->id),
            'action_label' => 'View Application',
        ];
    }
}
