<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiagnosaPrb;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', $this->getDashboardData());
    }

    public function data()
    {
        return response()->json($this->getDashboardData());
    }

    protected function getDashboardData()
    {
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';

        $kunjunganPerBulan = array_fill(1, 12, 0);

        $qBulanan = DiagnosaPrb::leftJoin('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->selectRaw('MONTH(diagnosa_prb.tgl_pelayanan) as bulan, COUNT(*) as total')
            ->whereYear('diagnosa_prb.tgl_pelayanan', Carbon::now()->year);

        $this->applyRoleFilter($qBulanan, $user, $isAdmin);

        $qBulanan->groupByRaw('MONTH(diagnosa_prb.tgl_pelayanan)')
            ->pluck('total', 'bulan')
            ->each(fn ($v, $k) => $kunjunganPerBulan[(int)$k] = (int)$v);

       $kunjunganPerBulanArray = array_values($kunjunganPerBulan);

    
        $bulanIni  = Carbon::now()->month;
        $tahunIni  = Carbon::now()->year;

        $qDiagnosa = DiagnosaPrb::leftJoin('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->select('diagnosa_prb.diagnosa', DB::raw('COUNT(*) as total'))
            ->whereMonth('diagnosa_prb.tgl_pelayanan', $bulanIni)
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni);

        $this->applyRoleFilter($qDiagnosa, $user, $isAdmin);

        $diagnosaTerbanyak = $qDiagnosa
            ->groupBy('diagnosa_prb.diagnosa')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $totalDiagnosa = $diagnosaTerbanyak->sum('total');

        $diagnosaChart = $diagnosaTerbanyak->map(fn ($d) => [
            'diagnosa' => $d->diagnosa,
            'persen'   => $totalDiagnosa > 0
                ? round(($d->total / $totalDiagnosa) * 100, 1)
                : 0
        ])->toArray();

       
        $kunjunganPerMinggu = array_fill(1, 7, 0);

        $qMingguan = DiagnosaPrb::leftJoin('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->selectRaw('DAYOFWEEK(diagnosa_prb.tgl_pelayanan) as hari, COUNT(*) as total')
            ->whereBetween('diagnosa_prb.tgl_pelayanan', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);

        $this->applyRoleFilter($qMingguan, $user, $isAdmin);

        $totals = $qMingguan->groupByRaw('DAYOFWEEK(diagnosa_prb.tgl_pelayanan)')
            ->pluck('total', 'hari')
            ->toArray();

        $order = [2,3,4,5,6,7,1];
        $kunjunganPerMingguArray = array_map(fn($d) => isset($totals[$d]) ? (int)$totals[$d] : 0, $order);

       
        $qPrb = DiagnosaPrb::leftJoin('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->leftJoin('users', 'patients.created_by', '=', 'users.id_user');

        $this->applyRoleFilter($qPrb, $user, $isAdmin);

        $dataPrbTerbaru = $qPrb->select(
                'diagnosa_prb.id_diagnosa',
                'patients.no_kartu_bpjs',
                'diagnosa_prb.diagnosa',
                'diagnosa_prb.status_prb',
                'diagnosa_prb.tgl_pelayanan',
                'users.username as rumah_sakit'
            )
            ->orderByDesc('diagnosa_prb.tgl_pelayanan')
            ->limit($isAdmin ? 10 : 5)
            ->get();

        return [
            'kunjunganPerBulan'  => $kunjunganPerBulanArray,
            'diagnosaChart'     => $diagnosaChart,
            'kunjunganPerMinggu'=> $kunjunganPerMingguArray,
            'dataPrbTerbaru'    => $dataPrbTerbaru,
        ];
    }

   
    private function applyRoleFilter($query, $user, $isAdmin)
    {
        if ($isAdmin) return;

        if ($user->role === 'fktp') {
            $query->where('patients.fktp_kode', $user->fktp_kode);
        } elseif ($user->role === 'apotek') {
            $query->where('patients.kode_apotek', $user->kode_apotek);
        } elseif ($user->role === 'rumah_sakit') {
            $query->where('patients.rumah_sakit_id', $user->rumah_sakit_id);
        } else {
            $query->where('patients.created_by', $user->id_user);
        }
    }
}
