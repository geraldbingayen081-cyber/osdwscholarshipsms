<?php

namespace App\Notifications;

use App\Models\WelfareCase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelfareCaseStatusUpdated extends Notification
{
    use Queueable;

    public WelfareCase $welfareCase;
    public string $message;

    public function __construct(WelfareCase $welfareCase, ?string $customMessage = null)
    {
        $this->welfareCase = $welfareCase;
        $this->message = $customMessage ?? "Your welfare case #{$welfareCase->case_id} ({$welfareCase->category}) status has been updated to: {$welfareCase->status}.";
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('student.welfare-cases.show', $this->welfareCase->id);

        $mail = (new MailMessage)
            ->subject("Welfare Case Update: [{$this->welfareCase->case_id}]")
            ->greeting("Hello {$notifiable->full_name},")
            ->line("There has been an update on your student welfare case.")
            ->line("**Case ID:** #{$this->welfareCase->case_id}")
            ->line("**Category:** {$this->welfareCase->category}")
            ->line("**Current Status:** {$this->welfareCase->status}");

        if (!empty($this->welfareCase->requested_information)) {
            $mail->line("**OSDW Staff Notes / Requested Information:** {$this->welfareCase->requested_information}");
        }

        return $mail->action('View Welfare Case Details', $url)
            ->line('Thank you for coordinating with the Office of Student Development and Welfare (OSDW).');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'welfare_case',
            'welfare_case_id' => $this->welfareCase->id,
            'case_id' => $this->welfareCase->case_id,
            'category' => $this->welfareCase->category,
            'status' => $this->welfareCase->status,
            'requested_information' => $this->welfareCase->requested_information,
            'message' => $this->message,
            'url' => route('student.welfare-cases.show', $this->welfareCase->id),
        ];
    }
}
