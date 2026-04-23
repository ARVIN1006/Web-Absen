<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected LeaveRequest $leaveRequest,
        protected string $action // 'submitted', 'approved', 'rejected'
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $user = $this->leaveRequest->user;

        return match ($this->action) {
            'submitted' => [
                'title' => 'Pengajuan Cuti Baru',
                'message' => "{$user->name} mengajukan cuti {$this->leaveRequest->total_days} hari.",
                'type' => 'leave_request',
                'action' => 'submitted',
                'leave_request_id' => $this->leaveRequest->id,
            ],
            'approved' => [
                'title' => 'Cuti Disetujui ✅',
                'message' => "Pengajuan cuti Anda ({$this->leaveRequest->total_days} hari) telah disetujui.",
                'type' => 'leave_request',
                'action' => 'approved',
                'leave_request_id' => $this->leaveRequest->id,
            ],
            'rejected' => [
                'title' => 'Cuti Ditolak ❌',
                'message' => "Pengajuan cuti Anda ditolak. Alasan: " . ($this->leaveRequest->admin_note ?? '-'),
                'type' => 'leave_request',
                'action' => 'rejected',
                'leave_request_id' => $this->leaveRequest->id,
            ],
        };
    }
}
