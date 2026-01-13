<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\DiagnosaPrb;
use App\Models\ObatPrb;
use App\Models\Claim;
use Carbon\Carbon;
use DB;

class ApotekDashboardController extends Controller
{
    public function index()
    {
        $kodeApotek = auth()->user()->kode_apotek;

        $totalPasien = Patient::where('kode_apotek', $kodeApotek)->count();

        $totalDiagnosa = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.kode_apotek', $kodeApotek)
            ->count();

        $totalObatKlaim = ObatPrb::join('diagnosa_prb', 'obat_prb.id_diagnosa', '=', 'diagnosa_prb.id_diagnosa')
            ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.kode_apotek', $kodeApotek)
            ->where('obat_prb.is_klaim', true)
            ->count();

        $totalKlaim = Claim::join('faskes', 'claims.pharmacy_id', '=', 'faskes.id')
            ->where('faskes.kode_faskes', $kodeApotek)
            ->count();

        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
    
        $resepBulanIni = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.kode_apotek', $kodeApotek)
            ->whereMonth('diagnosa_prb.tgl_pelayanan', $bulanIni)
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->count();

       
        $kunjunganBulanan = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.kode_apotek', $kodeApotek)
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

      
        $obatTerbanyak = ObatPrb::join('diagnosa_prb', 'obat_prb.id_diagnosa', '=', 'diagnosa_prb.id_diagnosa')
            ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.kode_apotek', $kodeApotek)
            ->where('obat_prb.is_klaim', true)
            ->select('obat_prb.nama_obat', DB::raw('COUNT(*) as total'))
            ->whereMonth('diagnosa_prb.tgl_pelayanan', $bulanIni)
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->groupBy('obat_prb.nama_obat')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $reminderPengambilan = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->leftJoin('obat_prb', 'diagnosa_prb.id_diagnosa', '=', 'obat_prb.id_diagnosa')
            ->where('patients.kode_apotek', $kodeApotek)
            ->whereBetween('diagnosa_prb.tgl_pelayanan', [Carbon::now()->subMonths(1)->subDays(5), Carbon::now()->addMonths(1)])
            ->select('diagnosa_prb.*', 'patients.nama_pasien', 'patients.no_kartu_bpjs', 'patients.no_telp', 'obat_prb.nama_obat')
            ->get()
            ->map(function($item) {
                $serviceDate = Carbon::parse($item->tgl_pelayanan);
                $pickupDate = $serviceDate->copy()->addMonths(1);
                $daysDiff = (int) Carbon::now()->diffInDays($pickupDate, false);
                if ($daysDiff >= 0 && $daysDiff <= 5) {
                    $item->type = 'H-' . $daysDiff;
                    $item->pickup_date = $pickupDate->format('Y-m-d');
                    $item->days_left = $daysDiff;
                    return $item;
                }
                return null;
            })
            ->filter();

        return view('dashboard.apotek', compact(
            'totalPasien',
            'totalDiagnosa',
            'totalObatKlaim',
            'totalKlaim',
            'resepBulanIni',
            'chartData',
            'obatTerbanyak',
            'reminderPengambilan'
        ));
    }

    public function notifications()
    {
        return response()->json(['data' => []]);
    }

    public function exportReminder(Request $request)
    {
        $kodeApotek = auth()->user()->kode_apotek;
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $startDate = Carbon::create($tahun, $bulan, 1)->subDays(5);
        $endDate = Carbon::create($tahun, $bulan, 1)->addMonth()->addDays(5);

        $reminder = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->join('obat_prb', function($join) {
                $join->on('diagnosa_prb.id_diagnosa', '=', 'obat_prb.id_diagnosa')
                     ->where('obat_prb.is_klaim', false);
            })
            ->where('patients.kode_apotek', $kodeApotek)
            ->whereBetween('diagnosa_prb.tgl_pelayanan', [$startDate, $endDate])
            ->select('diagnosa_prb.id_diagnosa', 'diagnosa_prb.diagnosa', 'diagnosa_prb.tgl_pelayanan', 'patients.nama_pasien', 'patients.no_kartu_bpjs', 'patients.no_telp', DB::raw('GROUP_CONCAT(obat_prb.nama_obat) as nama_obat'))
            ->groupBy('diagnosa_prb.id_diagnosa', 'diagnosa_prb.diagnosa', 'diagnosa_prb.tgl_pelayanan', 'patients.nama_pasien', 'patients.no_kartu_bpjs', 'patients.no_telp')
            ->get()
            ->map(function($item) {
                $serviceDate = Carbon::parse($item->tgl_pelayanan);
                $pickupDate = $serviceDate->copy()->addMonths(1);
                $daysDiff = (int) Carbon::now()->diffInDays($pickupDate, false);
                if ($daysDiff >= 0 && $daysDiff <= 5) {
                    return [
                        'Nama Pasien' => $item->nama_pasien,
                        'No Kartu BPJS' => $item->no_kartu_bpjs,
                        'No Telepon' => $item->no_telp,
                        'Diagnosa' => $item->diagnosa,
                        'Obat' => $item->nama_obat,
                        'Tgl Pelayanan Awal' => $item->tgl_pelayanan,
                        'Tgl Pengambilan Obat' => $pickupDate->format('Y-m-d'),
                        'Status' => 'H-' . $daysDiff
                    ];
                }
                return null;
            })
            ->filter();

        $filename = 'reminder_pengambilan_obat_' . $tahun . '_' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.xlsx';
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
            $sheet->setCellValue('E1', 'Obat');
            $sheet->setCellValue('F1', 'Tgl Pelayanan Awal');
            $sheet->setCellValue('G1', 'Tgl Pengambilan Obat');
            $sheet->setCellValue('H1', 'Status');

            $row = 2;
            foreach ($reminder as $item) {
                $sheet->setCellValue('A' . $row, $item['Nama Pasien']);
                $sheet->setCellValue('B' . $row, $item['No Kartu BPJS']);
                $sheet->setCellValue('C' . $row, $item['No Telepon']);
                $sheet->setCellValue('D' . $row, $item['Diagnosa']);
                $sheet->setCellValue('E' . $row, $item['Obat']);
                $sheet->setCellValue('F' . $row, $item['Tgl Pelayanan Awal']);
                $sheet->setCellValue('G' . $row, $item['Tgl Pengambilan Obat']);
                $sheet->setCellValue('H' . $row, $item['Status']);
                $row++;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $headers);
    }
}