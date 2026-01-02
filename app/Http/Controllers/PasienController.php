<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ObatPrb;
use App\Models\DiagnosaPrb;
use App\Models\RelasiFktpApotek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PasienController extends Controller
{
   public function index(Request $request)
{
    $query = Patient::with('diagnosaPrb');

    if (auth()->user()->role === 'rumah_sakit') {
        if (auth()->user()->rumah_sakit_id) {
            $rumahSakitUser = auth()->user()->rumahSakit;
           
            $userIds = \DB::table('users')
                ->where('rumah_sakit_id', auth()->user()->rumah_sakit_id)
                ->pluck('id_user');
            
            $query->whereIn('created_by', $userIds);
        }
    }

    if (auth()->user()->role === 'apotek') {

        $kodeApotek = auth()->user()->kode_apotek; 

        if ($kodeApotek) {


            $kodeFktpList = \DB::table('relasi_fktp_apotek')
                ->where('kode_apotek', $kodeApotek)
                ->pluck('kode_fktp');

            $query->whereIn('fktp_kode', $kodeFktpList);
        }
    }

    if (auth()->user()->role === 'fktp') {
        if (auth()->user()->fktp_kode) {
            $query->where('fktp_kode', auth()->user()->fktp_kode);
        }
    }


    if (auth()->user()->role === 'rumah_sakit' && $request->filled('input_role')) {
        $inputRole = $request->input_role;
        $query->whereHas('creator', function ($q) use ($inputRole) {
            $q->where('role', $inputRole);
        });
    }

    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereHas('diagnosaPrb', function ($q) use ($request) {
            $q->whereBetween('tgl_pelayanan', [$request->start_date, $request->end_date]);
        });
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('no_sep', 'like', "%{$search}%")
              ->orWhere('no_kartu_bpjs', 'like', "%{$search}%")
              ->orWhere('nama_pasien', 'like', "%{$search}%")
              ->orWhereHas('diagnosaPrb', function ($diagnosaQuery) use ($search) {
                  $diagnosaQuery->where('no_sep', 'like', "%{$search}%");
              });
        });
    }

    if ($request->filled('filter_no_sep')) {
        $query->where('no_sep', 'like', "%{$request->filter_no_sep}%");
    }

    if ($request->filled('filter_no_kartu_bpjs')) {
        $query->where('no_kartu_bpjs', 'like', "%{$request->filter_no_kartu_bpjs}%");
    }

    if ($request->filled('filter_diagnosa')) {
        $query->whereHas('diagnosaPrb', function ($q) use ($request) {
            $q->where('diagnosa', 'like', "%{$request->filter_diagnosa}%");
        });
    }

    if ($request->filled('filter_status_prb')) {
        $query->whereHas('diagnosaPrb', function ($q) use ($request) {
            $q->where('status_prb', $request->filter_status_prb);
        });
    }

    if ($request->filled('filter_tgl_pelayanan')) {
        $query->whereHas('diagnosaPrb', function ($q) use ($request) {
            $q->whereDate('tgl_pelayanan', $request->filter_tgl_pelayanan);
        });
    }

    if ($request->filled('filter_input_by')) {
        $query->whereHas('creator', function ($q) use ($request) {
            $q->where('role', $request->filter_input_by);
        });
    }


    $sortBy = $request->get('sort', 'created_at');
    $direction = $request->get('direction', 'desc');
    if (in_array($sortBy, ['no_sep', 'no_kartu_bpjs', 'nama_pasien', 'created_at'])) {
        $query->orderBy($sortBy, $direction);
    }

    $pasiens = $query->paginate(20);
    return view('pasien.index', compact('pasiens'));
}

public function create()
{
    $fktp = RelasiFktpApotek::orderBy('nama_fktp')->get();

    return view('pasien.create', compact('fktp'));
}

    public function store(Request $request)
    {

        $request->validate([
            'no_sep' => 'required|string',
            'no_kartu_bpjs' => 'required|string',
            'nama_pasien' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'fktp_asal' => 'required|string',
            'fktp_kode' => 'nullable|string',
            'kode_apotek' => 'nullable|string',
            'no_telp' => 'nullable|string',
            'diagnosa' => 'nullable|string',
            'status_prb' => 'nullable|string',
            'no_telp_pic' => 'nullable|string',
            'tgl_pemeriksaan' => 'nullable|date',
            'catatan_tambahan' => 'nullable|string',
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'nama_obat' => 'nullable|string',
            'jumlah_obat' => 'nullable|integer',
            'dosis_obat' => 'nullable|string',
            'aturan_pakai' => 'nullable|string',
        ]);

        $kodeFktp = $request->fktp_kode;
        $kodeApotek = $request->kode_apotek;

        if (empty($kodeFktp) && $request->filled('fktp_asal')) {
            $relasi = RelasiFktpApotek::where('nama_fktp', $request->fktp_asal)->first();
            if ($relasi) {
                $kodeFktp = $relasi->kode_fktp;
                if (empty($kodeApotek)) {
                    $kodeApotek = $relasi->kode_apotek;
                }
            }
        }

        
        $existingPatient = Patient::where('no_kartu_bpjs', $request->no_kartu_bpjs)->first();

        if ($existingPatient) {
           
            $jumlahKunjunganSebelumnya = $existingPatient->diagnosaPrb()->count();

            return redirect()->route('pasien.show', $existingPatient->id_pasien)
                ->with('info', 'Pasien dengan No Kartu BPJS ' . $request->no_kartu_bpjs . ' sudah terdaftar dengan ' . $jumlahKunjunganSebelumnya . ' kunjungan sebelumnya. Silakan tambahkan diagnosis baru menggunakan tombol "Tambah Diagnosis Baru".')
                ->withInput();
        } else {

            $pasien = Patient::create([
                'no_sep' => $request->no_sep,
                'no_kartu_bpjs' => $request->no_kartu_bpjs,
                'nama_pasien' => $request->nama_pasien,
                'tanggal_lahir' => $request->tanggal_lahir,
                'fktp_asal' => $request->fktp_asal,
                'fktp_kode' => $kodeFktp,
                'kode_apotek' => $kodeApotek,
                'no_telp' => $request->no_telp,
                'created_by' => auth()->id(),
            ]);
        }

        if ($request->filled('diagnosa')) {
            $fileName = null;
            if ($request->hasFile('file_upload')) {
                $file = $request->file('file_upload');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('diagnosa', $fileName, 'private');
            }

            $diagnosa = DiagnosaPrb::create([
                'id_pasien' => $pasien->id_pasien,
                'no_sep' => $request->no_sep,
                'diagnosa' => $request->diagnosa,
                'status_prb' => $request->status_prb,
                'no_telp_pic' => $request->no_telp_pic,
                'tgl_pelayanan' => $request->tgl_pemeriksaan,
                'catatan' => $request->catatan_tambahan,
                'file_upload' => $fileName,
            ]);

            if ($request->filled('nama_obat')) {
                ObatPrb::create([
                    'id_diagnosa' => $diagnosa->id_diagnosa,
                    'id_pasien' => $pasien->id_pasien,
                    'nama_obat' => $request->nama_obat,
                    'jumlah_obat' => $request->jumlah_obat,
                    'dosis_obat' => $request->dosis_obat,
                    'aturan_pakai' => $request->aturan_pakai,
                ]);
            }
        }

        return redirect()->route('pasien.index')->with('success', 'Data pasien berhasil disimpan.');
    }

   public function edit($id)
{
    $pasien = Patient::with(['diagnosaPrb.obatPrb'])->findOrFail($id);
    
    if (auth()->user()->role === 'rumah_sakit') {
        $userIds = \DB::table('users')
            ->where('rumah_sakit_id', auth()->user()->rumah_sakit_id)
            ->pluck('id_user');
        
        if (!$userIds->contains($pasien->created_by)) {
            abort(403, 'Anda tidak memiliki akses ke data pasien ini.');
        }
    }
    
    $diagnosa = $pasien->diagnosaPrb->first();
    $obat = $diagnosa ? $diagnosa->obatPrb : collect();
    $fktp = RelasiFktpApotek::orderBy('nama_fktp')->get();

    return view('pasien.edit', compact('pasien', 'diagnosa', 'obat', 'fktp'));
}

  public function update(Request $request, $id)
{
    $pasien = Patient::findOrFail($id);

    $request->validate([
        'no_sep' => 'required|string',
        'no_kartu_bpjs' => 'required|string',
        'nama_pasien' => 'required|string',
        'tanggal_lahir' => 'required|date',
        'fktp_asal' => 'required|string',
        'fktp_kode' => 'nullable|string',
        'no_telp' => 'nullable|string',
    ]);

    $kodeFktp = $request->fktp_kode;
    if (empty($kodeFktp) && $request->filled('fktp_asal')) {
        $kodeFktp = RelasiFktpApotek::where('nama_fktp', $request->fktp_asal)
                    ->value('kode_fktp');
    }

    $pasien->update([
        'no_sep' => $request->no_sep,
        'no_kartu_bpjs' => $request->no_kartu_bpjs,
        'nama_pasien' => $request->nama_pasien,
        'tanggal_lahir' => $request->tanggal_lahir,
        'fktp_asal' => $request->fktp_asal,
        'fktp_kode' => $kodeFktp,
        'no_telp' => $request->no_telp,
    ]);

    return back()->with('success', 'Data pasien berhasil diperbarui.');
}

    public function updateDiagnosa(Request $request, $id_diagnosa)
    {
        $request->validate([
            'diagnosa' => 'required|string|max:255',
            'diagnosa_lain' => 'nullable|string|max:255',
            'tgl_pelayanan' => 'required|date',
            'catatan' => 'nullable|string',
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'bukti_bayar_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $diagnosa = DiagnosaPrb::findOrFail($id_diagnosa);
        $diagnosaFinal = $request->diagnosa === 'Lainnya'
            ? $request->diagnosa_lain
            : $request->diagnosa;

        if ($request->hasFile('file_upload')) {
            if ($diagnosa->file_upload && Storage::disk('private')->exists('diagnosa/' . $diagnosa->file_upload)) {
                Storage::disk('private')->delete('diagnosa/' . $diagnosa->file_upload);
            }

            $file = $request->file('file_upload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('diagnosa', $fileName, 'private');
            $diagnosa->file_upload = $fileName;
        }

        if ($request->hasFile('bukti_bayar_pdf')) {
            if ($diagnosa->obatPrb->first() && $diagnosa->obatPrb->first()->bukti_bayar_pdf && Storage::disk('private')->exists('diagnosa/' . $diagnosa->obatPrb->first()->bukti_bayar_pdf)) {
                Storage::disk('private')->delete('diagnosa/' . $diagnosa->obatPrb->first()->bukti_bayar_pdf);
            }

            $file = $request->file('bukti_bayar_pdf');
            $fileName = time() . '_bukti_' . $file->getClientOriginalName();
            $file->storeAs('diagnosa', $fileName, 'private');

            $diagnosa->obatPrb()->update(['bukti_bayar_pdf' => $fileName]);
        }

        $diagnosa->update([
            'diagnosa' => $diagnosaFinal,
            'tgl_pelayanan' => $request->tgl_pelayanan,
            'status_prb' => $request->status_prb,
            'no_telp_pic' => $request->no_telp_pic,
            'catatan' => $request->catatan,
            'file_upload' => $diagnosa->file_upload,
        ]);

        return redirect()->route('pasien.edit', $diagnosa->id_pasien)->with('success', 'Diagnosa berhasil diperbarui.');
    }

    public function updateObat(Request $request, $id_obat = null)
    {
        $validated = $request->validate([
            'id_diagnosa' => 'required|exists:diagnosa_prb,id_diagnosa',
            'nama_obat' => 'required|string|max:255',
            'jumlah_obat' => 'required|integer',
            'satuan' => 'required|string|max:255',
            'dosis_obat' => 'nullable|string|max:255',
            'aturan_pakai' => 'nullable|string|max:255',
        ]);

        if ($id_obat) {
            $obat = ObatPrb::findOrFail($id_obat);
            $obat->update($validated);
        } else {
            ObatPrb::create($validated);
        }

        return redirect()->back()->with('success', 'Data obat berhasil disimpan.');
    }

    public function destroy($id)
    {
        $pasien = Patient::findOrFail($id);
        $pasien->delete();
        return back()->with('success', 'Data pasien berhasil dihapus.');
    }

    public function storeDiagnosa(Request $request)
    {
        $request->validate([
            'id_pasien' => 'required|exists:patients,id_pasien',
            'diagnosa' => 'required|string|max:255',
            'diagnosa_lain' => 'nullable|string|max:255',
            'tgl_pelayanan' => 'required|date',
            'no_telp_pic' => 'required|string',
            'status_prb' => 'required|string|max:100',
            'catatan' => 'nullable|string',
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ]);

        $fileName = null;
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('diagnosa', $fileName, 'private');
        }

        $diagnosaFinal = $request->diagnosa === 'Lainnya'
            ? $request->diagnosa_lain
            : $request->diagnosa;

        DB::table('diagnosa_prb')->insert([
            'id_pasien' => $request->id_pasien,
            'diagnosa' => $diagnosaFinal,
            'tgl_pelayanan' => $request->tgl_pelayanan,
            'status_prb' => 'Belum Aktif',
            'catatan' => $request->catatan,
            'no_telp_pic' => $request->no_telp_pic,
            'file_upload' => $fileName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('pasien.edit', $request->id_pasien)->with('success', 'Data diagnosa berhasil disimpan!');
    }

    public function storeObat(Request $request)
    {
        $request->validate([
            'id_diagnosa' => 'required|exists:diagnosa_prb,id_diagnosa',
            'nama_obat' => 'required|string|max:255',
            'jumlah_obat' => 'required|integer',
            'satuan' => 'nullable|string|max:50',
            'dosis_obat' => 'nullable|string|max:50',
            'aturan_pakai' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        ObatPrb::create([
            'id_diagnosa' => $request->id_diagnosa,
            'nama_obat' => $request->nama_obat,
            'jumlah_obat' => $request->jumlah_obat,
            'satuan' => $request->satuan,
            'dosis_obat' => $request->dosis_obat,
            'aturan_pakai' => $request->aturan_pakai,
        ]);

        return redirect()->back()->with('success', 'Data obat berhasil disimpan.');
    }

    public function show($id)
    {
        $pasien = Patient::findOrFail($id);
        
        if (auth()->user()->role === 'rumah_sakit') {
            $userIds = \DB::table('users')
                ->where('rumah_sakit_id', auth()->user()->rumah_sakit_id)
                ->pluck('id_user');
            
            if (!$userIds->contains($pasien->created_by)) {
                abort(403, 'Anda tidak memiliki akses ke data pasien ini.');
            }
        }

        $prbAktif = DiagnosaPrb::where('id_pasien', $id)->where('status_prb', 'Aktif')->with('obatPrb')->paginate(20);
        $prbRiwayat = DiagnosaPrb::where('id_pasien', $id)->where('status_prb', '!=', 'Aktif')->with('obatPrb')->orderBy('created_at', 'desc')->paginate(20);
        
        return view('pasien.show', compact('pasien', 'prbAktif', 'prbRiwayat'));
    }

    public function showFile($id)
    {
        $diagnosa = DB::table('diagnosa_prb')->where('id_diagnosa', $id)->first();

        if (!$diagnosa || !$diagnosa->file_upload) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = 'diagnosa/' . $diagnosa->file_upload;
        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        return Storage::disk('private')->response($filePath, $diagnosa->file_upload, [
            'Content-Disposition' => 'inline; filename="' . $diagnosa->file_upload . '"',
            'Content-Type' => Storage::disk('private')->mimeType($filePath) ?? 'application/octet-stream',
        ]);
    }

    public function addDiagnosis($id)
    {
        $pasien = Patient::findOrFail($id);

        if (auth()->user()->role === 'rumah_sakit') {
            $userIds = \DB::table('users')
                ->where('rumah_sakit_id', auth()->user()->rumah_sakit_id)
                ->pluck('id_user');

            if (!$userIds->contains($pasien->created_by)) {
                abort(403, 'Anda tidak memiliki akses ke data pasien ini.');
            }
        }

        $isCorrectFktp = false;
        if (auth()->user()->role === 'fktp') {
            $isCorrectFktp = auth()->user()->fktp_kode === $pasien->fktp_kode;
        }

        $fktp = RelasiFktpApotek::orderBy('nama_fktp')->get();

        return view('pasien.add-diagnosis', compact('pasien', 'isCorrectFktp', 'fktp'));
    }

    public function storeDiagnosis(Request $request, $id)
    {
        $pasien = Patient::findOrFail($id);

        if (auth()->user()->role === 'rumah_sakit') {
            $userIds = \DB::table('users')
                ->where('rumah_sakit_id', auth()->user()->rumah_sakit_id)
                ->pluck('id_user');

            if (!$userIds->contains($pasien->created_by)) {
                abort(403, 'Anda tidak memiliki akses ke data pasien ini.');
            }
        }

        if ($request->status_prb === 'Aktif' && auth()->user()->role === 'fktp') {
            if (auth()->user()->fktp_kode !== $pasien->fktp_kode) {
                return redirect()->back()->with('error', 'Hanya FKTP ' . $pasien->fktp_asal . ' yang dapat mengaktifkan status PRB untuk pasien ini.');
            }
        }

        if ($request->status_prb === 'Aktif' && auth()->user()->role !== 'fktp') {
            return redirect()->back()->with('error', 'Hanya FKTP yang dapat mengaktifkan status PRB.');
        }

        $request->validate([
            'no_sep' => 'required|string',
            'diagnosa' => 'required|string',
            'status_prb' => 'required|string',
            'no_telp_pic' => 'required|string',
            'tgl_pemeriksaan' => 'required|date',
            'catatan_tambahan' => 'nullable|string',
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'nama_obat' => 'nullable|string',
            'jumlah_obat' => 'nullable|integer',
            'dosis_obat' => 'nullable|string',
            'aturan_pakai' => 'nullable|string',
        ]);

        $fileName = null;
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('diagnosa', $fileName, 'private');
        }

        $diagnosa = DiagnosaPrb::create([
            'id_pasien' => $pasien->id_pasien,
            'no_sep' => $request->no_sep,
            'diagnosa' => $request->diagnosa,
            'status_prb' => $request->status_prb,
            'no_telp_pic' => $request->no_telp_pic,
            'tgl_pelayanan' => $request->tgl_pemeriksaan,
            'catatan' => $request->catatan_tambahan,
            'file_upload' => $fileName,
        ]);

        if ($request->filled('nama_obat')) {
            ObatPrb::create([
                'id_diagnosa' => $diagnosa->id_diagnosa,
                'id_pasien' => $pasien->id_pasien,
                'nama_obat' => $request->nama_obat,
                'jumlah_obat' => $request->jumlah_obat,
                'dosis_obat' => $request->dosis_obat,
                'aturan_pakai' => $request->aturan_pakai,
            ]);
        }

        return redirect()->route('pasien.show', $pasien->id_pasien)->with('success', 'Diagnosis baru berhasil ditambahkan ke riwayat pasien.');
    }
}
