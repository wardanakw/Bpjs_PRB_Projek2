<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\DiagnosaPrb;
use App\Models\ObatPrb;
use Carbon\Carbon;
use DB;

class FktpDashboardController extends Controller
{
    public function index()
    {
        $fktpKode = auth()->user()->fktp_kode;

        $totalPrbAktif = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.fktp_kode', $fktpKode)
            ->where('diagnosa_prb.status_prb', 'Aktif')
            ->count();

        $totalPasien = Patient::where('fktp_kode', $fktpKode)->count();

        $totalDiagnosa = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.fktp_kode', $fktpKode)
            ->count();

        $totalObat = ObatPrb::join('diagnosa_prb', 'obat_prb.id_diagnosa', '=', 'diagnosa_prb.id_diagnosa')
            ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.fktp_kode', $fktpKode)
            ->count();

        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $resepBulanIni = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.fktp_kode', $fktpKode)
            ->whereMonth('diagnosa_prb.tgl_pelayanan', $bulanIni)
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->count();

       
        $kunjunganBulanan = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.fktp_kode', $fktpKode)
            ->select(
                DB::raw('MONTH(diagnosa_prb.tgl_pelayanan) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->groupBy(DB::raw('MONTH(diagnosa_prb.tgl_pelayanan)'))
            ->orderBy(DB::raw('MONTH(diagnosa_prb.tgl_pelayanan)'))
            ->pluck('total', 'bulan')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $kunjunganBulanan[$i] ?? 0;
        }

        $diagnosaTerbanyak = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.fktp_kode', $fktpKode)
            ->select('diagnosa_prb.diagnosa', DB::raw('COUNT(*) as total'))
            ->whereMonth('diagnosa_prb.tgl_pelayanan', $bulanIni)
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->groupBy('diagnosa_prb.diagnosa')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $reminderFilter = request('reminder_filter', 'all');

        $today = Carbon::today();

$reminder = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
    ->where('patients.fktp_kode', $fktpKode)
    ->select(
        'diagnosa_prb.diagnosa',
        'diagnosa_prb.tgl_pelayanan',
        'patients.nama_pasien',
        'patients.no_kartu_bpjs',
        'patients.no_telp'
    )
    ->get()
    ->map(function ($item) use ($today) {

        $tglKunjungan = Carbon::parse($item->tgl_pelayanan)->addMonth();
        $h = $today->diffInDays($tglKunjungan, false);

        // TAMPILKAN H-5 s/d H-0
        if ($h >= 0 && $h <= 5) {
            return (object) [
                'nama_pasien' => $item->nama_pasien,
                'diagnosa'    => $item->diagnosa,
                'tgl_pelayanan_lanjutan' => $tglKunjungan->format('Y-m-d'),
                'days_left'   => $h,
                'type'        => $h === 0 ? 'H-0' : 'H-' . $h
            ];
        }

        return null;
    })
    ->filter()
    ->sortBy('days_left');


       if ($reminderFilter !== 'all') {
    $filterDays = (int) str_replace('h', '', $reminderFilter);

    $reminder = $reminder->filter(function ($item) use ($filterDays) {
        return isset($item->days_left) && $item->days_left === $filterDays;
    });
}


        return view('dashboard.fktp', compact(
            'totalPrbAktif',
            'totalPasien',
            'totalDiagnosa',
            'totalObat',
            'resepBulanIni',
            'chartData',
            'diagnosaTerbanyak',
            'reminder',
            'reminderFilter'
        ));
    }

    public function exportReminder()
    {
        $kodeFktp = auth()->user()->fktp_kode;
        $reminderFilter = request('filter', 'all');

        $reminder = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.fktp_kode', $kodeFktp)
            ->whereBetween('diagnosa_prb.tgl_pelayanan', [
                Carbon::now()->subMonths(1),
                Carbon::now()->subMonths(1)->addDays(5)
            ])
            ->select('diagnosa_prb.*', 'patients.nama_pasien', 'patients.no_kartu_bpjs', 'patients.no_telp')
            ->get()
            ->map(function($item) {
                $serviceDate = Carbon::parse($item->tgl_pelayanan);
                $followUpDate = $serviceDate->copy()->addMonths(1);
                $daysDiff = Carbon::now()->diffInDays($followUpDate, false);
                if ($daysDiff >= 0 && $daysDiff <= 5) {
                    return [
                        'Nama Pasien' => $item->nama_pasien,
                        'No Kartu BPJS' => $item->no_kartu_bpjs,
                        'No Telepon' => $item->no_telp,
                        'Diagnosa' => $item->diagnosa,
                        'Tgl Pelayanan Awal' => $item->tgl_pelayanan,
                        'Tgl Pelayanan Lanjutan' => $followUpDate->format('Y-m-d')
                    ];
                }
                return null;
            })
            ->filter();

        // Filter reminder berdasarkan reminderFilter
        if ($reminderFilter !== 'all') {
            $filterDays = (int) str_replace('h', '', $reminderFilter);
            $reminder = $reminder->filter(function($item) use ($filterDays) {
                $serviceDate = Carbon::parse($item['Tgl Pelayanan Awal']);
                $followUpDate = $serviceDate->copy()->addMonths(1);
                $daysDiff = Carbon::now()->diffInDays($followUpDate, false);
                return $daysDiff == $filterDays;
            });
        }

        $filename = 'reminder_pelayanan_lanjutan_' . date('Y-m-d') . '.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function() use ($reminder) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Nama Pasien');
            $sheet->setCellValue('B1', 'No Kartu BPJS');
            $sheet->setCellValue('C1', 'No Telepon');
            $sheet->setCellValue('D1', 'Diagnosa');
            $sheet->setCellValue('E1', 'Tgl Pelayanan Awal');
            $sheet->setCellValue('F1', 'Tgl Pelayanan Lanjutan');

            $row = 2;
            foreach ($reminder as $item) {
                $sheet->setCellValue('A' . $row, $item['Nama Pasien']);
                $sheet->setCellValue('B' . $row, $item['No Kartu BPJS']);
                $sheet->setCellValue('C' . $row, $item['No Telepon']);
                $sheet->setCellValue('D' . $row, $item['Diagnosa']);
                $sheet->setCellValue('E' . $row, $item['Tgl Pelayanan Awal']);
                $sheet->setCellValue('F' . $row, $item['Tgl Pelayanan Lanjutan']);
                $row++;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $headers);
    }
}
