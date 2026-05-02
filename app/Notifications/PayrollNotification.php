<?php

namespace App\Notifications;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PayrollNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Payroll $payroll,
        protected string $action
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $netSalary = 'Rp ' . number_format($this->payroll->net_salary, 0, ',', '.');
        $period = $this->payroll->month . '/' . $this->payroll->year;

        return match ($this->action) {
            'generated' => [
                'title' => 'Slip Gaji Dibuat',
                'message' => "Slip gaji periode {$period} telah dibuat. Total: {$netSalary}.",
                'type' => 'payroll',
                'action' => 'generated',
                'payroll_id' => $this->payroll->id,
            ],
            'paid' => [
                'title' => 'Gaji Telah Dibayarkan',
                'message' => "Gaji periode {$period} sebesar {$netSalary} telah dibayarkan.",
                'type' => 'payroll',
                'action' => 'paid',
                'payroll_id' => $this->payroll->id,
            ],
        };
    }
}
