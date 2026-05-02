<?php

namespace App\Notifications;

use App\Models\Reimbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReimbursementNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Reimbursement $reimbursement,
        protected string $action
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $user = $this->reimbursement->user;
        $amount = 'Rp ' . number_format($this->reimbursement->amount, 0, ',', '.');

        return match ($this->action) {
            'submitted' => [
                'title' => 'Pengajuan Reimbursement Baru',
                'message' => "{$user->name} mengajukan reimbursement {$amount} ({$this->reimbursement->title}).",
                'type' => 'reimbursement',
                'action' => 'submitted',
                'reimbursement_id' => $this->reimbursement->id,
            ],
            'approved' => [
                'title' => 'Reimbursement Disetujui',
                'message' => "Pengajuan reimbursement {$amount} ({$this->reimbursement->title}) telah disetujui.",
                'type' => 'reimbursement',
                'action' => 'approved',
                'reimbursement_id' => $this->reimbursement->id,
            ],
            'rejected' => [
                'title' => 'Reimbursement Ditolak',
                'message' => "Pengajuan reimbursement {$amount} ditolak. Alasan: " . ($this->reimbursement->admin_note ?? '-'),
                'type' => 'reimbursement',
                'action' => 'rejected',
                'reimbursement_id' => $this->reimbursement->id,
            ],
        };
    }
}
