<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiagnosaPrb;
use App\Models\Patient;
use App\Models\Faskes;
use Carbon\Carbon;
use DB;

class RumahSakitDashboardController extends Controller
{
    private function getHospitalPatients($rumahSakit)
    {
        return Patient::where('rumah_sakit_id', $rumahSakit->id);
    }

    private function getDiagnosaQuery($rumahSakit)
    {
        return DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.rumah_sakit_id', $rumahSakit->id);
    }

    public function index()
    {
        // Ambil rumah sakit milik user yang login
        $rumahSakit = Faskes::where('user_id', auth()->id())->first();

        // Jika user bukan admin RS, redirect
        if (!$rumahSakit) {
            return redirect()->route('dashboard.index');
        }

        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // ========== DATA UNTUK CHART BULANAN ==========
        $kunjunganBulanan = $this->getDiagnosaQuery($rumahSakit)
            ->select(
                DB::raw("MONTH(diagnosa_prb.tgl_pelayanan) as bulan"),
                DB::raw("COUNT(*) as total")
            )
            ->whereNotNull('diagnosa_prb.tgl_pelayanan')
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->groupBy(DB::raw("MONTH(diagnosa_prb.tgl_pelayanan)"))
            ->orderBy('bulan')
            ->get();

        
        $kunjunganPerBulan = array_fill(1, 12, 0);
      
        foreach ($kunjunganBulanan as $item) {
            if ($item->bulan >= 1 && $item->bulan <= 12) {
                $kunjunganPerBulan[$item->bulan] = (int)$item->total;
            }
        }

    
        $kunjunganPerBulanChart = array_values($kunjunganPerBulan);

    
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $kunjunganMingguan = $this->getDiagnosaQuery($rumahSakit)
            ->select(
                DB::raw("DAYOFWEEK(diagnosa_prb.tgl_pelayanan) as hari"),
                DB::raw("COUNT(*) as total")
            )
            ->whereNotNull('diagnosa_prb.tgl_pelayanan')
            ->whereBetween('diagnosa_prb.tgl_pelayanan', [$startOfWeek, $endOfWeek])
            ->groupBy(DB::raw("DAYOFWEEK(diagnosa_prb.tgl_pelayanan)"))
            ->get();

        $kunjunganPerHari = array_fill(1, 7, 0);
        
        foreach ($kunjunganMingguan as $item) {
            if ($item->hari >= 1 && $item->hari <= 7) {
                $kunjunganPerHari[$item->hari] = (int)$item->total;
            }
        }

       
        $chartMinggu = [
            $kunjunganPerHari[2] ?? 0, 
            $kunjunganPerHari[3] ?? 0, 
            $kunjunganPerHari[4] ?? 0, 
            $kunjunganPerHari[5] ?? 0, 
            $kunjunganPerHari[6] ?? 0, 
            $kunjunganPerHari[7] ?? 0, 
            $kunjunganPerHari[1] ?? 0  
        ];

    
        $diagnosaTerbanyak = $this->getDiagnosaQuery($rumahSakit)
            ->select('diagnosa_prb.diagnosa', DB::raw('COUNT(*) as total'))
            ->whereMonth('diagnosa_prb.tgl_pelayanan', $bulanIni)
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->groupBy('diagnosa_prb.diagnosa')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $totalDiagnosaBulanIni = $this->getDiagnosaQuery($rumahSakit)
            ->whereMonth('diagnosa_prb.tgl_pelayanan', $bulanIni)
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->count();

        $diagnosaChart = [];
        foreach ($diagnosaTerbanyak as $item) {
            $persen = $totalDiagnosaBulanIni > 0 
                ? round(($item->total / $totalDiagnosaBulanIni) * 100, 1)
                : 0;
                
            $diagnosaChart[] = [
                'diagnosa' => $item->diagnosa,
                'persen' => $persen,
                'total' => $item->total
            ];
        }

       
        $dataPrbTerbaru = $this->getDiagnosaQuery($rumahSakit)
            ->select(
                'diagnosa_prb.id_diagnosa',
                'patients.no_kartu_bpjs',
                'patients.nama_pasien',
                'diagnosa_prb.diagnosa',
                'diagnosa_prb.status_prb',
                'diagnosa_prb.tgl_pelayanan'
            )
            ->whereNotNull('diagnosa_prb.tgl_pelayanan')
            ->orderBy('diagnosa_prb.tgl_pelayanan', 'desc')
            ->limit(5)
            ->get();

        $totalPasien = $this->getHospitalPatients($rumahSakit)->count();
        $totalDiagnosa = $this->getDiagnosaQuery($rumahSakit)->count();
        $totalKunjunganBulanIni = $this->getDiagnosaQuery($rumahSakit)
            ->whereMonth('diagnosa_prb.tgl_pelayanan', $bulanIni)
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->count();

        return view('dashboard.rumahsakit', compact(
            'rumahSakit',
            'kunjunganPerBulanChart',
            'diagnosaChart',
            'chartMinggu',
            'dataPrbTerbaru',
            'totalPasien',
            'totalDiagnosa',
            'totalKunjunganBulanIni'
        ));
    }

    public function data()
    {
        $rumahSakit = Faskes::where('user_id', auth()->id())->first();

        if (!$rumahSakit) {
            return response()->json(['success' => false, 'error' => 'Rumah sakit tidak ditemukan'], 404);
        }

        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

    
        $kunjunganBulanan = $this->getDiagnosaQuery($rumahSakit)
            ->select(
                DB::raw("MONTH(diagnosa_prb.tgl_pelayanan) as bulan"),
                DB::raw("COUNT(*) as total")
            )
            ->whereNotNull('diagnosa_prb.tgl_pelayanan')
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->groupBy(DB::raw("MONTH(diagnosa_prb.tgl_pelayanan)"))
            ->orderBy('bulan')
            ->get();

        $kunjunganPerBulan = array_fill(1, 12, 0);
        foreach ($kunjunganBulanan as $item) {
            $kunjunganPerBulan[$item->bulan] = (int)$item->total;
        }

    
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $kunjunganMingguan = $this->getDiagnosaQuery($rumahSakit)
            ->select(
                DB::raw("DAYOFWEEK(diagnosa_prb.tgl_pelayanan) as hari"),
                DB::raw("COUNT(*) as total")
            )
            ->whereNotNull('diagnosa_prb.tgl_pelayanan')
            ->whereBetween('diagnosa_prb.tgl_pelayanan', [$startOfWeek, $endOfWeek])
            ->groupBy(DB::raw("DAYOFWEEK(diagnosa_prb.tgl_pelayanan)"))
            ->get();

        $kunjunganPerHari = array_fill(1, 7, 0);
        foreach ($kunjunganMingguan as $item) {
            $kunjunganPerHari[$item->hari] = (int)$item->total;
        }

        $totalPasien = $this->getHospitalPatients($rumahSakit)->count();
        $totalDiagnosa = $this->getDiagnosaQuery($rumahSakit)->count();
        $totalKunjunganBulanIni = $this->getDiagnosaQuery($rumahSakit)
            ->whereMonth('diagnosa_prb.tgl_pelayanan', $bulanIni)
            ->whereYear('diagnosa_prb.tgl_pelayanan', $tahunIni)
            ->count();

        return response()->json([
            'success' => true,
            'kunjunganPerBulan' => array_values($kunjunganPerBulan),
            'kunjunganPerMinggu' => [
                $kunjunganPerHari[2] ?? 0,
                $kunjunganPerHari[3] ?? 0,
                $kunjunganPerHari[4] ?? 0,
                $kunjunganPerHari[5] ?? 0,
                $kunjunganPerHari[6] ?? 0,
                $kunjunganPerHari[7] ?? 0,
                $kunjunganPerHari[1] ?? 0
            ],
            'totalPasien' => $totalPasien,
            'totalDiagnosa' => $totalDiagnosa,
            'totalKunjunganBulanIni' => $totalKunjunganBulanIni
        ]);
    }
}