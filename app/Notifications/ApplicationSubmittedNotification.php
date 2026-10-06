<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmittedNotification extends Notification
{
    use Queueable;

    public Application $application;
    public bool $forAdmin;

    public function __construct(Application $application, bool $forAdmin = false)
    {
        $this->application = $application;
        $this->forAdmin = $forAdmin;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $scholarshipName = $this->application->scholarship->name ?? 'Scholarship';
        $studentName = $this->application->student->user->full_name ?? 'Student';

        if ($this->forAdmin) {
            return (new MailMessage)
                ->subject("New Scholarship Application: {$scholarshipName}")
                ->greeting("Hello {$notifiable->full_name},")
                ->line("A new scholarship application has been submitted by {$studentName}.")
                ->line("**Scholarship:** {$scholarshipName}")
                ->line("**Student:** {$studentName} (" . ($this->application->student->student_number ?? 'N/A') . ")")
                ->action('Review Application', route('admin.applications.show', $this->application->id));
        }

        return (new MailMessage)
            ->subject("Application Received: {$scholarshipName}")
            ->greeting("Hello {$notifiable->full_name},")
            ->line("Your application for the **{$scholarshipName}** scholarship has been successfully submitted and received by the OSDW.")
            ->line("Our team will review your submitted documents and information. You will receive real-time updates as your application progresses.")
            ->action('View My Application', route('student.applications.show', $this->application->id))
            ->line('Thank you for applying through CSU-Lal-lo OSDW Scholarship Management System.');
    }

    public function toArray(object $notifiable): array
    {
        $scholarshipName = $this->application->scholarship->name ?? 'Scholarship';

        if ($this->forAdmin) {
            $studentName = $this->application->student->user->full_name ?? 'Student';
            return [
                'type' => 'admin_application_new',
                'application_id' => $this->application->id,
                'scholarship_id' => $this->application->scholarship_id,
                'scholarship_name' => $scholarshipName,
                'student_name' => $studentName,
                'message' => "New application submitted by {$studentName} for '{$scholarshipName}'.",
                'url' => route('admin.applications.show', $this->application->id),
            ];
        }

        return [
            'type' => 'application_submitted',
            'application_id' => $this->application->id,
            'scholarship_id' => $this->application->scholarship_id,
            'scholarship_name' => $scholarshipName,
            'status' => 'submitted',
            'message' => "Your application for '{$scholarshipName}' has been submitted successfully.",
            'url' => route('student.applications.show', $this->application->id),
            'action_label' => 'Track Application',
        ];
    }
}
