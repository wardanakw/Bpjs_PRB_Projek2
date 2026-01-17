<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\DiagnosaPrb;
use App\Models\ObatPrb;
use Carbon\Carbon;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ApotekLaporanController extends Controller
{
    public function laporanObatKeluar(Request $request)
    {
        $kodeApotek = auth()->user()->kode_apotek;
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Data obat yang keluar dalam bulan tertentu (berdasarkan tanggal_klaim) dengan pagination
        $obatKeluar = ObatPrb::join('diagnosa_prb', 'obat_prb.id_diagnosa', '=', 'diagnosa_prb.id_diagnosa')
            ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.kode_apotek', $kodeApotek)
            ->where('obat_prb.is_klaim', true)
            ->whereNotNull('obat_prb.tanggal_klaim')
            ->whereMonth('obat_prb.tanggal_klaim', $bulan)
            ->whereYear('obat_prb.tanggal_klaim', $tahun)
            ->select('obat_prb.nama_obat', DB::raw('COUNT(*) as total'))
            ->groupBy('obat_prb.nama_obat')
            ->orderByDesc('total')
            ->paginate(10);

        // Total obat yang keluar (ambil dari database tanpa pagination)
        $totalObat = ObatPrb::join('diagnosa_prb', 'obat_prb.id_diagnosa', '=', 'diagnosa_prb.id_diagnosa')
            ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.kode_apotek', $kodeApotek)
            ->where('obat_prb.is_klaim', true)
            ->whereNotNull('obat_prb.tanggal_klaim')
            ->whereMonth('obat_prb.tanggal_klaim', $bulan)
            ->whereYear('obat_prb.tanggal_klaim', $tahun)
            ->count();

        // Data untuk chart
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Ringkasan obat keluar selama 12 bulan terakhir (berdasarkan tanggal_klaim)
        $obatKeluar12Bulan = [];
        $now = Carbon::now();
        
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $m = $date->month;
            $y = $date->year;
            
            $count = ObatPrb::join('diagnosa_prb', 'obat_prb.id_diagnosa', '=', 'diagnosa_prb.id_diagnosa')
                ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
                ->where('patients.kode_apotek', $kodeApotek)
                ->where('obat_prb.is_klaim', true)
                ->whereNotNull('obat_prb.tanggal_klaim')
                ->whereMonth('obat_prb.tanggal_klaim', $m)
                ->whereYear('obat_prb.tanggal_klaim', $y)
                ->count();
            
            $obatKeluar12Bulan[] = [
                'bulan' => $namaBulan[$m],
                'total' => $count,
                'tahun' => $y
            ];
        }

        return view('apotek.laporan-obat-keluar', compact(
            'obatKeluar',
            'totalObat',
            'bulan',
            'tahun',
            'namaBulan',
            'obatKeluar12Bulan'
        ));
    }

    public function exportLaporanObatKeluar(Request $request)
    {
        $kodeApotek = auth()->user()->kode_apotek;
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Ambil semua data obat untuk di-export
        $obatKeluar = ObatPrb::join('diagnosa_prb', 'obat_prb.id_diagnosa', '=', 'diagnosa_prb.id_diagnosa')
            ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.kode_apotek', $kodeApotek)
            ->where('obat_prb.is_klaim', true)
            ->whereNotNull('obat_prb.tanggal_klaim')
            ->whereMonth('obat_prb.tanggal_klaim', $bulan)
            ->whereYear('obat_prb.tanggal_klaim', $tahun)
            ->select('obat_prb.nama_obat', DB::raw('COUNT(*) as total'))
            ->groupBy('obat_prb.nama_obat')
            ->orderByDesc('total')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'LAPORAN OBAT KELUAR - ' . strtoupper($namaBulan[$bulan]) . ' ' . $tahun);
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Berdasarkan tanggal klaim obat');
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        
        // Header
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Nama Obat');
        $sheet->setCellValue('C4', 'Jumlah');
        
        $sheet->getStyle('A4:C4')->getFont()->setBold(true);
        $sheet->getStyle('A4:C4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3');
        
        // Data
        $row = 5;
        $no = 1;
        foreach ($obatKeluar as $obat) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $obat->nama_obat);
            $sheet->setCellValue('C' . $row, $obat->total);
            $row++;
            $no++;
        }

        // Total row
        $totalRow = $row;
        $sheet->setCellValue('B' . $totalRow, 'TOTAL');
        $sheet->setCellValue('C' . $totalRow, '=SUM(C5:C' . ($totalRow - 1) . ')');
        $sheet->getStyle('B' . $totalRow . ':C' . $totalRow)->getFont()->setBold(true);
        $sheet->getStyle('B' . $totalRow . ':C' . $totalRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFFCC');

        // Set column width
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(12);

        // Center alignment for number columns
        $sheet->getStyle('A4:A' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C4:C' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $filename = 'laporan_obat_keluar_' . $namaBulan[$bulan] . '_' . $tahun . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
