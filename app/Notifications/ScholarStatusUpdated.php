<?php

namespace App\Notifications;

use App\Models\Scholar;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ScholarStatusUpdated extends Notification
{
    use Queueable;

    public Scholar $scholar;
    public string $message;

    public function __construct(Scholar $scholar)
    {
        $this->scholar = $scholar;
        $scholarshipName = $scholar->scholarship->name ?? 'Scholarship Program';
        $statusStr = ucfirst(str_replace('_', ' ', $scholar->status));

        $this->message = "Your scholar standing for '{$scholarshipName}' is now: {$statusStr}.";
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'scholar_id' => $this->scholar->id,
            'scholarship_id' => $this->scholar->scholarship_id,
            'scholarship_name' => $this->scholar->scholarship->name ?? '',
            'status' => $this->scholar->status,
            'message' => $this->message,
            'url' => route('student.applications.index'),
        ];
    }
}
