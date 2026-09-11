<?php

namespace App\Notifications;

use App\Models\Scholarship;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RenewalRequirementsRequested extends Notification
{
    use Queueable;

    public Scholarship $scholarship;
    public string $deadline;
    public string $semester;
    public string $schoolYear;
    public string $note;
    public array $requirements;

    public function __construct(Scholarship $scholarship, string $deadline, string $semester = '1st Semester', string $schoolYear = '2026-2027', string $note = '', array $requirements = [])
    {
        $this->scholarship = $scholarship;
        $this->deadline = $deadline;
        $this->semester = $semester;
        $this->schoolYear = $schoolYear;
        $this->note = $note;
        $this->requirements = $requirements;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $reqStr = !empty($this->requirements) 
            ? ' Requirements needed: ' . implode(', ', $this->requirements) . '.'
            : '';

        $noteStr = !empty($this->note) ? " Note: {$this->note}" : '';

        return [
            'scholarship_id' => $this->scholarship->id,
            'scholarship_name' => $this->scholarship->name,
            'semester' => $this->semester,
            'school_year' => $this->schoolYear,
            'deadline' => $this->deadline,
            'message' => "Renewal requirements requested for '{$this->scholarship->name}' ({$this->semester} SY {$this->schoolYear}). Please submit your documents on or before {$this->deadline}.{$reqStr}{$noteStr}",
            'url' => route('student.dashboard'),
        ];
    }
}
