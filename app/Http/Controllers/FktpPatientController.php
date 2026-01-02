<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\RelasiFktpApotek;
use App\Models\ObatPrb;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\DiagnosaPrb;
use DB;

class FktpPatientController extends Controller
{
   public function index()
{
    $user = auth()->user();
    if ($user->role === 'apotek') {
        $kodeApotek = $user->kode_apotek;
        $fktpKodes = RelasiFktpApotek::where('kode_apotek', $kodeApotek)->pluck('kode_fktp')->toArray();

        $patients = Patient::whereIn('fktp_kode', $fktpKodes)
            ->whereHas('diagnosaPrb.obatPrb', function($q) {
                $q->where('is_klaim', false);
            })
            ->with(['diagnosaPrb.obatPrb'])
            ->get();
    } else {
        $kode = $user->fktp_kode;
        $patients = Patient::where('fktp_kode', $kode)->with(['diagnosaPrb.obatPrb'])->get();
    }

    return view('fktp.patients.index', compact('patients'));
}

public function edit($id)
{
    $patient = Patient::findOrFail($id);
    $user = auth()->user();

    if ($user->role === 'apotek') {
        $fktpKodes = RelasiFktpApotek::where('kode_apotek', $user->kode_apotek)->pluck('kode_fktp')->toArray();
        if (!in_array($patient->fktp_kode, $fktpKodes)) {
            abort(403, 'Tidak boleh akses pasien FKTP lain');
        }
    } else {
        if ($patient->fktp_kode !== $user->fktp_kode) {
            abort(403, 'Tidak boleh akses pasien FKTP lain');
        }
    }

    return view('fktp.patients.edit', compact('patient'));
}

    public function update(Request $request, $id)
{
    $request->validate([
        'no_kunjungan' => 'required'
    ]);

    $patient = Patient::findOrFail($id);
    $patient->update([
        'no_kunjungan' => $request->no_kunjungan
    ]);

    \DB::table('diagnosa_prb')
        ->where('id_pasien', $patient->id_pasien)
        ->update(['status_prb' => 'Aktif']);

    $prefix = auth()->user()->role === 'apotek' ? 'apotek' : 'fktp';

    return redirect()->route($prefix . '.patients.index')
        ->with('success', 'No Kunjungan berhasil ditambahkan dan status PRB otomatis aktif.');
}

  public function show($id)
{
    $pasien = Patient::findOrFail($id);
    $user = auth()->user();

    if ($user->role === 'apotek') {
        $fktpKodes = RelasiFktpApotek::where('kode_apotek', $user->kode_apotek)->pluck('kode_fktp')->toArray();
        if (!in_array($pasien->fktp_kode, $fktpKodes)) {
            abort(403, 'Tidak boleh akses pasien FKTP lain');
        }
    } else {
        if ($pasien->fktp_kode !== $user->fktp_kode) {
            abort(403, 'Tidak boleh akses pasien FKTP lain');
        }
    }

    $prbAktif = DiagnosaPrb::where('id_pasien', $id)->where('status_prb', 'Aktif')->with('obatPrb')->paginate(20);
    $prbRiwayat = DiagnosaPrb::where('id_pasien', $id)->where('status_prb', '!=', 'Aktif')->with('obatPrb')->orderBy('created_at', 'desc')->paginate(20);

    return view('fktp.patients.show', compact('pasien', 'prbAktif', 'prbRiwayat'));
}


public function klaimDiagnosa(Request $request, $idDiagnosa)
{
    $diagnosa = DiagnosaPrb::findOrFail($idDiagnosa);
    $pasien = $diagnosa->patient;
    $user = auth()->user();

    if ($user->role !== 'apotek') {
        abort(403, 'Hanya apotek yang dapat melakukan klaim obat');
    }

    $fktpKodes = RelasiFktpApotek::where('kode_apotek', $user->kode_apotek)->pluck('kode_fktp')->toArray();
    if (!in_array($pasien->fktp_kode, $fktpKodes)) {
        abort(403, 'Tidak boleh akses pasien FKTP lain');
    }

    $obatBelumKlaim = $diagnosa->obatPrb->where('is_klaim', false);

    $firstObat = $obatBelumKlaim->first();
    if (empty($firstObat->bukti_bayar_pdf)) {
        return redirect()->back()->with('error', 'Harap upload file PDF bukti bayar untuk diagnosa ini terlebih dahulu sebelum melakukan klaim.');
    }

    if ($obatBelumKlaim->count() == 0) {
        return redirect()->back()->with('info', 'Tidak ada obat yang perlu diklaim.');
    }

    foreach ($obatBelumKlaim as $obat) {
        $obat->update([
            'is_klaim' => true,
            'tanggal_klaim' => Carbon::now(),
        ]);
    }

    $admins = \App\Models\User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        $admin->notify(new \App\Notifications\KlaimObatNotification($obatBelumKlaim->first())); // kirim untuk obat pertama
    }

    return redirect()->back()->with('success', 'Semua obat untuk pasien ini berhasil diklaim dan masuk ke riwayat');
}

public function riwayatObatKlaim(Request $request)
{
    $user = auth()->user();

    if ($user->role !== 'apotek') {
        abort(403, 'Hanya apotek yang dapat mengakses riwayat obat');
    }

    $fktpKodes = RelasiFktpApotek::where('kode_apotek', $user->kode_apotek)->pluck('kode_fktp')->toArray();

    $query = ObatPrb::whereHas('diagnosaPrb.patient', function($q) use ($fktpKodes) {
        $q->whereIn('fktp_kode', $fktpKodes);
    })
    ->where('is_klaim', true)
    ->with('diagnosaPrb.patient')
    ->orderByDesc('tanggal_klaim');

    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('diagnosaPrb.patient', function($patientQuery) use ($search) {
                $patientQuery->where('nama_pasien', 'like', '%' . $search . '%')
                             ->orWhere('no_kartu_bpjs', 'like', '%' . $search . '%');
            })
            ->orWhere('nama_obat', 'like', '%' . $search . '%');
        });
    }

    $obatKlaim = $query->paginate(20);

    return view('fktp.obat.riwayat-klaim', compact('obatKlaim'));
}

    
    public function apotekNotifications(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'apotek') {
            return response()->json(['data' => []]);
        }

        $fktpKodes = RelasiFktpApotek::where('kode_apotek', $user->kode_apotek)->pluck('kode_fktp')->toArray();

        $today = Carbon::today();

        $obats = ObatPrb::join('diagnosa_prb', 'obat_prb.id_diagnosa', '=', 'diagnosa_prb.id_diagnosa')
            ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->whereIn('patients.fktp_kode', $fktpKodes)
            ->where('obat_prb.is_klaim', false)
            ->where(function($q) use ($today) {
                $q->whereDate(DB::raw('DATE_ADD(diagnosa_prb.tgl_pelayanan, INTERVAL 1 MONTH)'), $today)
                  ->orWhereDate(DB::raw('DATE_SUB(DATE_ADD(diagnosa_prb.tgl_pelayanan, INTERVAL 1 MONTH), INTERVAL 3 DAY)'), $today);
            })
            ->select(
                'obat_prb.id_obat',
                'obat_prb.nama_obat',
                'diagnosa_prb.id_diagnosa',
                'patients.no_kartu_bpjs',
                'patients.nama_pasien',
                'patients.no_telp',
                'diagnosa_prb.tgl_pelayanan',
                'diagnosa_prb.diagnosa'
            )
            ->get();

        $items = $obats->map(function($o) use ($today) {
            $pickupDate = Carbon::parse($o->tgl_pelayanan)->addMonth();
            $h3Date = $pickupDate->copy()->subDays(3);

            $type = 'pickup';
            if ($today->eq($h3Date)) {
                $type = 'h-3';
            } elseif ($today->eq($pickupDate)) {
                $type = 'h-0';
            }

            return [
                'id_obat' => $o->id_obat,
                'nama_obat' => $o->nama_obat,
                'id_diagnosa' => $o->id_diagnosa,
                'no_kartu_bpjs' => $o->no_kartu_bpjs ?? null,
                'nama_pasien' => $o->nama_pasien ?? null,
                'no_telp' => $o->no_telp ?? null,
                'diagnosa' => $o->diagnosa,
                'tgl_pelayanan' => $o->tgl_pelayanan,
                'pickup_date' => $pickupDate->toDateString(),
                'type' => $type,
            ];
        })->values();

        return response()->json(['data' => $items]);
    }

    public function uploadKlaimPdf(Request $request, $idDiagnosa)
    {
        $user = auth()->user();
        if ($user->role !== 'apotek') {
            abort(403, 'Hanya apotek yang dapat meng-upload file klaim');
        }

        $diagnosa = DiagnosaPrb::findOrFail($idDiagnosa);

        $fktpKodes = RelasiFktpApotek::where('kode_apotek', $user->kode_apotek)->pluck('kode_fktp')->toArray();
        if (!in_array($diagnosa->patient->fktp_kode, $fktpKodes)) {
            abort(403, 'Tidak boleh akses pasien FKTP lain');
        }

        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:5120', 
        ]);

        $file = $request->file('pdf');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('diagnosa', $fileName, 'private');

        $diagnosa->obatPrb()->update(['bukti_bayar_pdf' => $fileName]);

        return redirect()->back()->with('success', 'File PDF berhasil di-upload. Anda sekarang dapat melakukan klaim.');
    }

    public function fktpNotifications()
    {
        $user = auth()->user();
        if ($user->role !== 'fktp') {
            return response()->json(['data' => []]);
        }

        $kodeFktp = $user->fktp_kode;
        $today = Carbon::today();
        $startDate = $today->copy()->subMonths(1); 
        $endDate = $today->copy()->addMonths(6); 
        $notifications = DiagnosaPrb::join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien')
            ->where('patients.fktp_kode', $kodeFktp)
            ->whereNull('diagnosa_prb.no_sep')
            ->whereBetween('diagnosa_prb.tgl_pelayanan', [$startDate, $endDate])
            ->select(
                'diagnosa_prb.id_diagnosa',
                'patients.nama_pasien',
                'patients.no_kartu_bpjs',
                'patients.no_telp',
                'diagnosa_prb.tgl_pelayanan',
                'diagnosa_prb.diagnosa'
            )
            ->get()
            ->map(function($item) use ($today) {
                $serviceDate = Carbon::parse($item->tgl_pelayanan);
                $followUpDate = $serviceDate->copy()->addMonths(1); 
                $daysDiff = $today->diffInDays($followUpDate, false); 
                if ($daysDiff < 0 || $daysDiff > 5) {
                    return null; 
                }

                $type = 'H-' . $daysDiff;
                return [
                    'id_diagnosa' => $item->id_diagnosa,
                    'nama_pasien' => $item->nama_pasien,
                    'no_kartu_bpjs' => $item->no_kartu_bpjs,
                    'no_telp' => $item->no_telp,
                    'diagnosa' => $item->diagnosa,
                    'tgl_pelayanan' => $item->tgl_pelayanan,
                    'tgl_pelayanan_lanjutan' => $followUpDate->format('Y-m-d'),
                    'type' => $type,
                    'days_left' => $daysDiff
                ];
            })
            ->filter() 
            ->values(); 

        return response()->json(['data' => $notifications]);
    }
}