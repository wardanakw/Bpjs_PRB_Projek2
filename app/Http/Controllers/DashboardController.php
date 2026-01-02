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
        $data = $this->getDashboardData();

        return view('dashboard.index', $data);
    }

    /**
     * Return dashboard data as JSON (used for polling / realtime updates)
     */
    public function data(Request $request)
    {
        $data = $this->getDashboardData();
        return response()->json($data);
    }

    /**
     * Build dashboard data filtered by the authenticated user.
     */
    protected function getDashboardData()
    {
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';

        $query = DiagnosaPrb::with('patient');

        if (!$isAdmin) {
            $query->whereHas('patient', function($q) use ($user) {
                $q->where('created_by', $user->id_user);
            });
        }

        $kunjunganBulanan = $query->select(
            DB::raw("MONTH(tgl_pelayanan) as bulan"),
            DB::raw("COUNT(*) as total")
        )
        ->whereYear('tgl_pelayanan', Carbon::now()->year)
        ->groupBy(DB::raw('MONTH(tgl_pelayanan)'))
        ->orderBy(DB::raw('MONTH(tgl_pelayanan)'))
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

        $diagnosaChart = $diagnosaTerbanyak->map(function($item) use ($totalDiagnosaBulanIni) {
            return [
                'diagnosa' => $item->diagnosa,
                'persen' => $totalDiagnosaBulanIni > 0
                    ? round(($item->total / $totalDiagnosaBulanIni) * 100, 1)
                    : 0
            ];
        });

        $kunjunganMingguan = $query->select(
            DB::raw("DAYOFWEEK(tgl_pelayanan) as hari"),
            DB::raw("COUNT(*) as total")
        )
        ->whereBetween('tgl_pelayanan', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])
        ->groupBy('hari')
        ->pluck('total', 'hari');

        $kunjunganPerMinggu = array_fill(1, 7, 0);
        foreach ($kunjunganMingguan as $hari => $total) {
            $kunjunganPerMinggu[$hari] = $total;
        }

        $dataPrbQuery = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->join('users', 'patients.created_by', '=', 'users.id_user');

        if (!$isAdmin) {
            $dataPrbQuery->where('patients.created_by', $user->id_user);
        }

        $dataPrbTerbaru = $dataPrbQuery->select(
            'diagnosa_prb.id_diagnosa',
            'patients.no_kartu_bpjs',
            'diagnosa_prb.diagnosa',
            'diagnosa_prb.status_prb',
            'diagnosa_prb.tgl_pelayanan',
            'users.username as rumah_sakit'
        )
        ->orderBy('diagnosa_prb.tgl_pelayanan', 'desc')
        ->take($isAdmin ? 10 : 5)
        ->get();

        return [
            'kunjunganPerBulan' => $kunjunganPerBulan,
            'diagnosaChart' => $diagnosaChart,
            'kunjunganPerMinggu' => $kunjunganPerMinggu,
            'dataPrbTerbaru' => $dataPrbTerbaru,
        ];
    }
}
