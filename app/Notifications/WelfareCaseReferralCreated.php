<?php

namespace App\Notifications;

use App\Models\WelfareCaseReferral;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelfareCaseReferralCreated extends Notification
{
    use Queueable;

    public WelfareCaseReferral $referral;

    public function __construct(WelfareCaseReferral $referral)
    {
        $this->referral = $referral;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $caseId = $this->referral->welfareCase->case_id ?? 'N/A';
        $caseUrl = route('student.welfare-cases.show', $this->referral->welfare_case_id);

        if ($this->referral->isScholarshipReferral()) {
            $scholarshipName = $this->referral->scholarship->name ?? 'Scholarship Program';
            $applyUrl = $this->referral->scholarship_id ? route('student.applications.create', $this->referral->scholarship_id) : $caseUrl;

            return (new MailMessage)
                ->subject("Scholarship Referral Notice - Case #{$caseId}")
                ->greeting("Hello {$notifiable->full_name},")
                ->line("Good news! The Office of Student Development and Welfare has reviewed your welfare case and provided an official scholarship referral.")
                ->line("**Referred Scholarship:** {$scholarshipName}")
                ->line("**Referral Note:** {$this->referral->referral_note}")
                ->action('Submit Scholarship Application', $applyUrl)
                ->line('Please note: This referral serves as an endorsement. You are still required to submit the official scholarship application and fulfill all program requirements.');
        }

        $officeName = $this->referral->referred_to_office ?? 'University Office';

        return (new MailMessage)
            ->subject("Official Referral Notice - Case #{$caseId} to {$officeName}")
            ->greeting("Hello {$notifiable->full_name},")
            ->line("The Office of Student Development and Welfare has reviewed your welfare concern and officially referred your case for specialized coordination.")
            ->line("**Referred To:** {$officeName}")
            ->line("**Staff Referral Note & Next Steps:** {$this->referral->referral_note}")
            ->action('View Welfare Case Details', $caseUrl)
            ->line('Please coordinate with the designated office as advised.');
    }

    public function toArray(object $notifiable): array
    {
        $caseId = $this->referral->welfareCase->case_id ?? '';

        if ($this->referral->isScholarshipReferral()) {
            $scholarshipName = $this->referral->scholarship->name ?? 'Scholarship Program';
            $url = $this->referral->scholarship_id ? route('student.applications.create', $this->referral->scholarship_id) : route('student.welfare-cases.show', $this->referral->welfare_case_id);

            return [
                'type' => 'welfare_referral',
                'referral_type' => 'scholarship',
                'welfare_case_id' => $this->referral->welfare_case_id,
                'referral_id' => $this->referral->id,
                'case_id' => $caseId,
                'scholarship_id' => $this->referral->scholarship_id,
                'scholarship_name' => $scholarshipName,
                'referral_note' => $this->referral->referral_note,
                'message' => "Your welfare case #{$caseId} has been referred to '{$scholarshipName}'. You may now submit your scholarship application.",
                'url' => $url,
                'action_label' => 'Apply to Scholarship',
            ];
        }

        $officeName = $this->referral->referred_to_office ?? 'University Office';

        return [
            'type' => 'welfare_referral',
            'referral_type' => 'office',
            'welfare_case_id' => $this->referral->welfare_case_id,
            'referral_id' => $this->referral->id,
            'case_id' => $caseId,
            'referred_to_office' => $officeName,
            'referral_note' => $this->referral->referral_note,
            'message' => "Your welfare case #{$caseId} has been officially referred to '{$officeName}'. Please review the instructions.",
            'url' => route('student.welfare-cases.show', $this->referral->welfare_case_id),
            'action_label' => 'View Referral Details',
        ];
    }
}
