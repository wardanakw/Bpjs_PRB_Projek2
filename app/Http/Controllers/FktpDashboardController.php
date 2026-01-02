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

        $reminder = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.fktp_kode', $fktpKode)
            ->whereBetween('diagnosa_prb.tgl_pelayanan', [Carbon::now()->subMonths(1), Carbon::now()->addMonths(6)])
            ->select('diagnosa_prb.*', 'patients.nama_pasien', 'patients.no_kartu_bpjs', 'patients.no_telp')
            ->get()
            ->map(function($item) {
                $serviceDate = Carbon::parse($item->tgl_pelayanan);
                $followUpDate = $serviceDate->copy()->addMonths(1);
                $daysDiff = Carbon::now()->diffInDays($followUpDate, false);
                if ($daysDiff >= 0 && $daysDiff <= 5) {
                    $item->type = 'H-' . $daysDiff;
                    $item->tgl_pelayanan_lanjutan = $followUpDate->format('Y-m-d');
                    $item->days_left = $daysDiff;
                    return $item;
                }
                return null;
            })
            ->filter();

        return view('dashboard.fktp', compact(
            'totalPrbAktif',
            'totalPasien',
            'totalDiagnosa',
            'totalObat',
            'resepBulanIni',
            'chartData',
            'diagnosaTerbanyak',
            'reminder'
        ));
    }

    public function exportReminder()
    {
        $kodeFktp = auth()->user()->fktp_kode;
        $reminder = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.fktp_kode', $kodeFktp)
            ->whereBetween('diagnosa_prb.tgl_pelayanan', [Carbon::now()->subMonths(1), Carbon::now()->addMonths(6)])
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
