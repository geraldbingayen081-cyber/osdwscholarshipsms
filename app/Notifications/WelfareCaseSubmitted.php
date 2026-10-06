<?php

namespace App\Notifications;

use App\Models\WelfareCase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelfareCaseSubmitted extends Notification
{
    use Queueable;

    public WelfareCase $welfareCase;

    public function __construct(WelfareCase $welfareCase)
    {
        $this->welfareCase = $welfareCase;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->welfareCase->student->user->full_name ?? 'A student';
        $studentNo = $this->welfareCase->student->student_number ?? 'N/A';
        $caseUrl = url('/admin/welfare-cases/' . $this->welfareCase->id);

        return (new MailMessage)
            ->subject("New Welfare Case Reported: #{$this->welfareCase->case_id} ({$this->welfareCase->category})")
            ->greeting("Hello {$notifiable->full_name},")
            ->line("A new student welfare concern has been reported and is awaiting review.")
            ->line("**Student:** {$studentName} ({$studentNo})")
            ->line("**Category:** {$this->welfareCase->category}")
            ->line("**Case ID:** #{$this->welfareCase->case_id}")
            ->line("**Description Summary:** " . \Illuminate\Support\Str::limit($this->welfareCase->description, 150))
            ->action('Review Welfare Case', $caseUrl)
            ->line('Please assess this case in the OSDW Admin Panel.');
    }

    public function toArray(object $notifiable): array
    {
        $studentName = $this->welfareCase->student->user->full_name ?? 'A student';

        return [
            'type' => 'admin_welfare_case_new',
            'welfare_case_id' => $this->welfareCase->id,
            'case_id' => $this->welfareCase->case_id,
            'category' => $this->welfareCase->category,
            'student_name' => $studentName,
            'message' => "New welfare concern reported by {$studentName}: #{$this->welfareCase->case_id} ({$this->welfareCase->category}).",
            'url' => url('/admin/welfare-cases/' . $this->welfareCase->id),
        ];
    }
}
