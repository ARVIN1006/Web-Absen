<?php

namespace App\Notifications;

use App\Models\AttendanceCorrection;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceCorrectionNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected AttendanceCorrection $attendanceCorrection,
        protected string $action
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $user = $this->attendanceCorrection->user;
        $attendanceDate = optional($this->attendanceCorrection->attendance_date)->format('d M Y');

        return match ($this->action) {
            'submitted' => [
                'title' => 'Koreksi Absensi Baru',
                'message' => "{$user->name} mengajukan koreksi absensi untuk {$attendanceDate}.",
                'type' => 'attendance_correction',
                'action' => 'submitted',
                'attendance_correction_id' => $this->attendanceCorrection->id,
            ],
            'approved' => [
                'title' => 'Koreksi Absensi Disetujui',
                'message' => "Koreksi absensi Anda untuk {$attendanceDate} telah disetujui.",
                'type' => 'attendance_correction',
                'action' => 'approved',
                'attendance_correction_id' => $this->attendanceCorrection->id,
            ],
            'rejected' => [
                'title' => 'Koreksi Absensi Ditolak',
                'message' => "Koreksi absensi Anda untuk {$attendanceDate} ditolak. Catatan: " . ($this->attendanceCorrection->admin_note ?: '-'),
                'type' => 'attendance_correction',
                'action' => 'rejected',
                'attendance_correction_id' => $this->attendanceCorrection->id,
            ],
        };
    }
}
