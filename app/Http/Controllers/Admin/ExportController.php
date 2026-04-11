<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function csv(Request $request)
    {
        $attendances = $this->getFilteredData($request);

        $filename = "laporan_absensi_" . date('Ymd_His') . ".csv";

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('No', 'Nama', 'Email', 'Jabatan', 'Tipe Absen', 'Tanggal', 'Waktu', 'Status', 'Lokasi (Lat, Lng)');

        $callback = function() use($attendances, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $count = 1;
            foreach ($attendances as $row) {
                fputcsv($file, array(
                    $count++,
                    $row->user->name,
                    $row->user->email,
                    $row->user->position ?? '-',
                    $row->type == 'in' ? 'Masuk' : 'Pulang',
                    $row->created_at->format('Y-m-d'),
                    $row->created_at->format('H:i:s'),
                    $row->status,
                    $row->latitude . ', ' . $row->longitude
                ));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pdf(Request $request)
    {
        $attendances = $this->getFilteredData($request);
        $period = 'Seluruh Waktu';
        if($request->start_date && $request->end_date) {
            $period = $request->start_date . ' s/d ' . $request->end_date;
        }

        $pdf = Pdf::loadView('admin.export-pdf', compact('attendances', 'period'));
        return $pdf->download("laporan_absensi_" . date('Ymd_His') . ".pdf");
    }

    private function getFilteredData(Request $request)
    {
        $query = Attendance::with('user')->orderBy('created_at', 'asc');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        return $query->get();
    }
}
