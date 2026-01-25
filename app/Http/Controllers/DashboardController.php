<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiagnosaPrb;
use App\Models\Patient;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Cek jika user adalah rumah sakit, redirect ke dashboard rumah sakit
        if (auth()->check() && auth()->user()->role === 'rumah_sakit') {
            return redirect()->route('rumahsakit.dashboard');
        }

        // Ambil semua data (untuk admin)
        $kunjunganBulanan = DiagnosaPrb::select(
            DB::raw("MONTH(tgl_pelayanan) as bulan"),
            DB::raw("COUNT(*) as total")
        )
            ->whereYear('tgl_pelayanan', Carbon::now()->year)
            ->groupBy(DB::raw("MONTH(tgl_pelayanan)"))
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $kunjunganPerBulan = array_fill(1, 12, 0);
        foreach ($kunjunganBulanan as $bulan => $total) {
            $kunjunganPerBulan[$bulan] = $total;
        }

        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $diagnosaTerbanyak = DiagnosaPrb::select('diagnosa', DB::raw('COUNT(*) as total'))
            ->whereMonth('tgl_pelayanan', $bulanIni)
            ->whereYear('tgl_pelayanan', $tahunIni)
            ->groupBy('diagnosa')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $totalDiagnosaBulanIni = DiagnosaPrb::whereMonth('tgl_pelayanan', $bulanIni)
            ->whereYear('tgl_pelayanan', $tahunIni)
            ->count();

        $diagnosaChart = $diagnosaTerbanyak->map(function ($item) use ($totalDiagnosaBulanIni) {
            return [
                'diagnosa' => $item->diagnosa,
                'persen' => $totalDiagnosaBulanIni > 0
                    ? round(($item->total / $totalDiagnosaBulanIni) * 100, 1)
                    : 0
            ];
        });

        $kunjunganMingguan = DiagnosaPrb::select(
            DB::raw("DAYOFWEEK(tgl_pelayanan) as hari"),
            DB::raw("COUNT(*) as total")
        )
            ->whereBetween('tgl_pelayanan', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->groupBy(DB::raw("DAYOFWEEK(tgl_pelayanan)"))
            ->pluck('total', 'hari');

        $kunjunganPerMinggu = array_fill(1, 7, 0);
        foreach ($kunjunganMingguan as $hari => $total) {
            $kunjunganPerMinggu[$hari] = $total;
        }

        $hariChart = [
            2 => $kunjunganPerMinggu[2] ?? 0,
            3 => $kunjunganPerMinggu[3] ?? 0,
            4 => $kunjunganPerMinggu[4] ?? 0,
            5 => $kunjunganPerMinggu[5] ?? 0,
            6 => $kunjunganPerMinggu[6] ?? 0,
            7 => $kunjunganPerMinggu[7] ?? 0,
            1 => $kunjunganPerMinggu[1] ?? 0
        ];

        $chartMinggu = array_values($hariChart);

        $dataPrbTerbaru = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->select(
                'diagnosa_prb.id_diagnosa',
                'patients.no_kartu_bpjs',
                'diagnosa_prb.diagnosa',
                'diagnosa_prb.status_prb',
                'diagnosa_prb.tgl_pelayanan'
            )
            ->orderBy('diagnosa_prb.tgl_pelayanan', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'kunjunganPerBulan',
            'diagnosaChart',
            'chartMinggu',
            'dataPrbTerbaru'
        ));
    }

    public function data()
    {
        // Untuk admin, ambil semua data
        $kunjunganBulanan = DiagnosaPrb::select(
            DB::raw("MONTH(tgl_pelayanan) as bulan"),
            DB::raw("COUNT(*) as total")
        )
            ->whereYear('tgl_pelayanan', Carbon::now()->year)
            ->groupBy(DB::raw("MONTH(tgl_pelayanan)"))
            ->orderBy('bulan')
            ->get();

        $kunjunganPerBulan = array_fill(1, 12, 0);
        foreach ($kunjunganBulanan as $item) {
            $kunjunganPerBulan[$item->bulan] = $item->total;
        }

        $kunjunganMingguan = DiagnosaPrb::select(
            DB::raw("DAYOFWEEK(tgl_pelayanan) as hari"),
            DB::raw("COUNT(*) as total")
        )
            ->whereBetween('tgl_pelayanan', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->groupBy(DB::raw("DAYOFWEEK(tgl_pelayanan)"))
            ->get();

        $kunjunganPerMinggu = array_fill(1, 7, 0);
        foreach ($kunjunganMingguan as $item) {
            $kunjunganPerMinggu[$item->hari] = $item->total;
        }

        return response()->json([
            'kunjunganPerBulan' => array_values($kunjunganPerBulan),
            'kunjunganPerMinggu' => [
                $kunjunganPerMinggu[2] ?? 0,
                $kunjunganPerMinggu[3] ?? 0,
                $kunjunganPerMinggu[4] ?? 0,
                $kunjunganPerMinggu[5] ?? 0,
                $kunjunganPerMinggu[6] ?? 0,
                $kunjunganPerMinggu[7] ?? 0,
                $kunjunganPerMinggu[1] ?? 0
            ]
        ]);
    }
}
