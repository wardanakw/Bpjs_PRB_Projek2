<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Faskes;
use Illuminate\Support\Facades\Hash;

class AdminFaskesController extends Controller
{

    public function index(Request $request)
    {
        $query = Faskes::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_faskes', 'like', "%{$search}%")
                  ->orWhere('kode_faskes', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('user', function ($userQuery) use ($request) {
                $userQuery->where('role', $request->role);
            });
        }

        $faskes = $query->latest()->get();
        $relasi = \App\Models\RelasiFktpApotek::select('kode_apotek', 'nama_apotek')->distinct()->get();

        return view('admin.faskes.index', compact('faskes', 'relasi'));
    }

    public function create()
    {
        $relasi = \App\Models\RelasiFktpApotek::select('kode_apotek', 'nama_apotek')->distinct()->get();
        return view('admin.faskes.create', compact('relasi'));
    }

    public function store(Request $request)
    {
    $isFktp = $request->role === 'fktp';
    $isApotek = $request->role === 'apotek';

    $validated = $request->validate([
        'name' => 'required|string',
        'username' => 'required|unique:users,username',
        'password' => 'required|string|min:6',
        'role' => 'required|in:fktp,apotek,rumah_sakit,admin',
        'nama_faskes' => 'required|string',
        'jenis_faskes' => 'nullable|string',
        'alamat_faskes' => 'nullable|string',
        'kecamatan' => 'nullable|string',
        'kabupaten' => 'nullable|string',
        'provinsi' => 'nullable|string',
        'kode_pos' => 'nullable|string',
        'kode_faskes' => $isFktp ? 'required|string|unique:faskes,kode_faskes' : 'nullable|string',
        'kode_apotek' => $isApotek ? 'required|string' : 'nullable|string'
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'username' => $validated['username'],
        'password' => $validated['password'], 
        'role' => $validated['role'],
        'fktp_kode' => $validated['role'] === 'fktp' ? $validated['kode_faskes'] : null,
        'kode_apotek' => $validated['role'] === 'apotek' ? $validated['kode_apotek'] : null,
    ]);

    $faskes = Faskes::create([
        'kode_faskes' => $validated['kode_faskes'] ?? null,
        'nama_faskes' => $validated['nama_faskes'],
        'jenis_faskes' => $validated['jenis_faskes'] ?? null,
        'alamat_faskes' => $validated['alamat_faskes'] ?? null,
        'kecamatan' => $validated['kecamatan'] ?? null,
        'kabupaten' => $validated['kabupaten'] ?? null,
        'provinsi' => $validated['provinsi'] ?? null,
        'kode_pos' => $validated['kode_pos'] ?? null,
        'user_id' => $user->id_user
    ]);

    if ($validated['role'] === 'rumah_sakit') {
        $user->update(['rumah_sakit_id' => $faskes->id]);
    }

    return redirect()->back()->with('success', 'User & Faskes berhasil dibuat');
    }

    public function show($id)
    {
        $faskes = Faskes::with('user')->findOrFail($id);
        return view('admin.faskes.show', compact('faskes'));
    }

    public function edit($id)
    {
        $faskes = Faskes::with('user')->findOrFail($id);
        $relasi = \App\Models\RelasiFktpApotek::select('kode_apotek', 'nama_apotek')->distinct()->get();

        return view('admin.faskes.edit', compact('faskes', 'relasi'));
    }

    public function update(Request $request, $id)
    {
        $faskes = Faskes::findOrFail($id);
        $user = $faskes->user;
        $request->validate([
            'nama_faskes' => 'required',
            'jenis_faskes' => 'required',
            'alamat_faskes' => 'required',

            'username' => 'required|unique:users,username,' . $user->id_user . ',id_user',

            'kode_faskes' => $request->role === 'fktp'
                                ? 'required'
                                : 'nullable',

            'kode_apotek' => $request->role === 'apotek'
                                ? 'required'
                                : 'nullable',
        ]);

        $user->update([
            'username'    => $request->username,
            'fktp_kode'   => $request->role === 'fktp' ? $request->kode_faskes : null,
            'kode_apotek' => $request->role === 'apotek' ? $request->kode_apotek : null,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $faskes->update([
            'fktp_kode'   => $request->role === 'fktp' ? $request->kode_faskes : null,
            'kode_apotek' => $request->role === 'apotek' ? $request->kode_apotek : null,
            'nama_faskes' => $request->nama_faskes,
            'jenis_faskes'=> $request->jenis_faskes,
            'alamat_faskes'=> $request->alamat_faskes,
            'kecamatan'   => $request->kecamatan,
            'kabupaten'   => $request->kabupaten,
            'provinsi'    => $request->provinsi,
            'kode_pos'    => $request->kode_pos,
        ]);

        return redirect()->route('admin.faskes.index')->with('success', 'Data faskes berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $faskes = Faskes::findOrFail($id);
        $faskes->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function showDetail($id, Request $request)
    {
        $faskes = Faskes::with('user')->findOrFail($id);
        
       
        if ($faskes->user->role !== 'rumah_sakit') {
            return back()->with('error', 'Hanya dapat menampilkan detail untuk Rumah Sakit.');
        }

        $userIds = \DB::table('users')
            ->where('rumah_sakit_id', $faskes->id)
            ->pluck('id_user');

        
        $query = \App\Models\Patient::with('diagnosaPrb', 'creator')
            ->whereIn('created_by', $userIds);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_sep', 'like', "%{$search}%")
                  ->orWhere('no_kartu_bpjs', 'like', "%{$search}%")
                  ->orWhere('nama_pasien', 'like', "%{$search}%");
            });
        }

        
        if ($request->filled('input_role')) {
            $query->whereHas('creator', function ($q) use ($request) {
                $q->where('role', $request->input_role);
            });
        }

       
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('diagnosaPrb', function ($q) use ($request) {
                $q->whereBetween('tgl_pelayanan', [$request->start_date, $request->end_date]);
            });
        }

        $pasiens = $query->get();

        return view('admin.faskes.detail', compact('faskes', 'pasiens'));
    }
}
