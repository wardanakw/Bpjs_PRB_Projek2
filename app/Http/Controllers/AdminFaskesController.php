<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Faskes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        try {
            
            $relasi = DB::table('relasi_fktp_apotek')
                ->select('kode_apotek', 'nama_apotek')
                ->orderBy('nama_apotek')
                ->get();
            
            return view('admin.faskes.create', compact('relasi'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat form');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
        
            $baseRules = [
                'name'         => 'required|string|max:100',
                'username'     => 'required|string|max:50|unique:users,username',
                'password'     => 'required|string|min:6|max:100',
                'role'         => 'required|in:fktp,apotek,rumah_sakit',
                'nama_faskes'  => 'required|string|max:150',
                'jenis_faskes' => 'nullable|string|max:50',
                'alamat_faskes'=> 'nullable|string|max:255',
                'kecamatan'    => 'nullable|string|max:50',
                'kabupaten'    => 'nullable|string|max:50',
                'provinsi'     => 'nullable|string|max:50',
                'kode_pos'     => 'nullable|string|max:10|regex:/^[0-9]+$/',
                'nomor_pic'    => 'nullable|string|max:15|regex:/^[0-9]+$/',
            ];
            
           
            $validated = $request->validate($baseRules);
            
            $kodeFaskes = null;
            $kodeApotek = null;
            
            if ($request->role === 'apotek') {
              
                $request->validate([
                    'kode_apotek' => 'required|string|max:20|exists:relasi_fktp_apotek,kode_apotek',
                ]);
                
    
                $kodeFaskes = $request->kode_apotek;
                $kodeApotek = $request->kode_apotek;
                
                $existingFaskes = Faskes::where('kode_faskes', $kodeFaskes)->first();
                if ($existingFaskes) {
                    throw ValidationException::withMessages([
                        'kode_apotek' => 'Kode apotek ini sudah digunakan oleh faskes lain.'
                    ]);
                }
            } else {
        
                $request->validate([
                    'kode_faskes' => 'required|string|max:20|unique:faskes,kode_faskes',
                ]);
                
                $kodeFaskes = $request->kode_faskes;
                $kodeApotek = null;
            }
    
            $validated['kode_faskes'] = $kodeFaskes;
            $validated['kode_apotek'] = $kodeApotek;
            
            $userData = [
                'name'     => $validated['name'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role'     => $validated['role'],
            ];
            
            switch ($validated['role']) {
                case 'fktp':
                    $userData['fktp_kode'] = $validated['kode_faskes'];
                    break;
                case 'apotek':
                    $userData['kode_apotek'] = $validated['kode_apotek'];
                    break;
            }
            
            $user = User::create($userData);
            
            $faskesData = [
                'kode_faskes'  => $validated['kode_faskes'],
                'nama_faskes'  => $validated['nama_faskes'],
                'jenis_faskes' => $validated['jenis_faskes'] ?? null,
                'alamat_faskes'=> $validated['alamat_faskes'] ?? null,
                'kecamatan'    => $validated['kecamatan'] ?? null,
                'kabupaten'    => $validated['kabupaten'] ?? null,
                'provinsi'     => $validated['provinsi'] ?? null,
                'kode_pos'     => $validated['kode_pos'] ?? null,
                'nomor_pic'    => $validated['nomor_pic'] ?? null,
                'user_id'      => $user->id_user,
                'kode_apotek'  => $validated['kode_apotek'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
            
            $faskes = Faskes::create($faskesData);
            
            if ($validated['role'] === 'rumah_sakit') {
                $user->update(['rumah_sakit_id' => $faskes->id]);
            }
            
        
            DB::commit();
            
            return redirect()
                ->route('admin.faskes.index')
                ->with('success', 'Fasilitas Kesehatan berhasil ditambahkan!')
                ->with('new_faskes_id', $faskes->id);
                
        } catch (ValidationException $e) {
            DB::rollBack();
            
            return back()
                ->withErrors($e->errors())
                ->withInput($request->except(['password']))
                ->with('error', 'Validasi gagal. Silakan periksa data Anda.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput($request->except(['password']))
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        
        $rules = [
            'name'         => 'required|string|max:100',
            'nama_faskes'  => 'required|string|max:150',
            'jenis_faskes' => 'required|string|max:50',
            'alamat_faskes'=> 'required|string|max:255',
            'kecamatan'    => 'nullable|string|max:50',
            'kabupaten'    => 'nullable|string|max:50',
            'provinsi'     => 'nullable|string|max:50',
            'kode_pos'     => 'nullable|string|max:10|regex:/^[0-9]+$/',
            'nomor_pic'    => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'username'     => 'required|unique:users,username,' . $user->id_user . ',id_user',
            'password'     => 'nullable|string|min:6|max:100',
        ];
        
       
        if ($user->role === 'fktp') {
            $rules['kode_faskes'] = 'required|string|max:20';
        } elseif ($user->role === 'apotek') {
            $rules['kode_apotek'] = 'required|string|max:20';
        } elseif ($user->role === 'rumah_sakit') {
            $rules['kode_faskes'] = 'required|string|max:20';
        }
        
        $validated = $request->validate($rules);

        $user->update([
            'name'        => $validated['name'],
            'username'    => $validated['username'],
            'fktp_kode'   => $user->role === 'fktp' ? $validated['kode_faskes'] : null,
            'kode_apotek' => $user->role === 'apotek' ? $validated['kode_apotek'] : null,
            'rumah_sakit_id' => $user->role === 'rumah_sakit' ? $faskes->id : $user->rumah_sakit_id,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $faskesUpdate = [
            'nama_faskes' => $validated['nama_faskes'],
            'jenis_faskes'=> $validated['jenis_faskes'],
            'alamat_faskes'=> $validated['alamat_faskes'],
            'kecamatan'   => $validated['kecamatan'] ?? null,
            'kabupaten'   => $validated['kabupaten'] ?? null,
            'provinsi'    => $validated['provinsi'] ?? null,
            'kode_pos'    => $validated['kode_pos'] ?? null,
            'nomor_pic'   => $validated['nomor_pic'] ?? null,
        ];

    
        if ($user->role === 'apotek') {
            $faskesUpdate['kode_faskes'] = $user->kode_apotek;
        } else {
            $faskesUpdate['kode_faskes'] = $validated['kode_faskes'];
        }

        $faskes->update($faskesUpdate);

        return redirect()->route('admin.faskes.index')->with('success', 'Data faskes berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $faskes = Faskes::findOrFail($id);

        if ($faskes->user) {
            $faskes->user->delete();
        }

        \App\Models\RelasiFktpApotek::where('nama_fktp', $faskes->nama_faskes)
            ->orWhere('nama_apotek', $faskes->nama_faskes)
            ->delete();

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
