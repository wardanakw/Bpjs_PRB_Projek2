<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Faskes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            Log::error('Error loading faskes create form: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        
        Log::info('=== FASKES STORE PROCESS START ===');
        Log::info('Request Data:', $request->except(['password', '_token']));
        
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
                
                Log::info('Processing APOTEK with kode: ' . $kodeFaskes);
                
                // Cek duplikasi
                $existingFaskes = Faskes::where('kode_faskes', $kodeFaskes)->first();
                if ($existingFaskes) {
                    throw ValidationException::withMessages([
                        'kode_apotek' => 'Kode apotek ini sudah digunakan oleh faskes lain.'
                    ]);
                }
            } else {
                // Validasi untuk non-apotek (FKTP & Rumah Sakit)
                $request->validate([
                    'kode_faskes' => 'required|string|max:20|unique:faskes,kode_faskes',
                ]);
                
                $kodeFaskes = $request->kode_faskes;
                $kodeApotek = null;
                
                Log::info('Processing ' . strtoupper($request->role) . ' with kode: ' . $kodeFaskes);
            }
            
            // Tambahkan kode ke validated data
            $validated['kode_faskes'] = $kodeFaskes;
            $validated['kode_apotek'] = $kodeApotek;
            
            Log::info('Final validated data:', [
                'role' => $validated['role'],
                'kode_faskes' => $validated['kode_faskes'],
                'kode_apotek' => $validated['kode_apotek']
            ]);
        
            $userData = [
                'name'     => $validated['name'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role'     => $validated['role'],
            ];
            
            // Tambahkan kode khusus berdasarkan role
            switch ($validated['role']) {
                case 'fktp':
                    $userData['fktp_kode'] = $validated['kode_faskes'];
                    break;
                case 'apotek':
                    $userData['kode_apotek'] = $validated['kode_apotek'];
                    break;
            }
            
            $user = User::create($userData);
            Log::info('User created successfully - ID: ' . $user->id_user);
            
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
            Log::info('Faskes created successfully - ID: ' . $faskes->id);
            
            // Update rumah_sakit_id jika role rumah sakit
            if ($validated['role'] === 'rumah_sakit') {
                $user->update(['rumah_sakit_id' => $faskes->id]);
                Log::info('Updated rumah_sakit_id for user: ' . $user->id_user);
            }
            
            // Commit transaction
            DB::commit();
            
            Log::info('=== FASKES STORE PROCESS COMPLETED SUCCESSFULLY ===');
            
            return redirect()
                ->route('admin.faskes.index')
                ->with('success', 'Fasilitas Kesehatan berhasil ditambahkan!')
                ->with('new_faskes_id', $faskes->id);
                
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation Error: ' . json_encode($e->errors()));
            
            return back()
                ->withErrors($e->errors())
                ->withInput($request->except(['password']))
                ->with('error', 'Validasi gagal. Silakan periksa data Anda.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Error: ' . $e->getMessage());
            Log::error('Error Trace: ' . $e->getTraceAsString());
            
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
