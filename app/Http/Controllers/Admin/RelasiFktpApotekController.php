<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RelasiFktpApotek;
use App\Models\Faskes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RelasiFktpApotekController extends Controller
{
    public function index(Request $request)
    {
        $query = RelasiFktpApotek::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_fktp', 'like', "%{$search}%")
                  ->orWhere('nama_fktp', 'like', "%{$search}%")
                  ->orWhere('nama_apotek', 'like', "%{$search}%")
                  ->orWhere('kode_apotek', 'like', "%{$search}%");
            });
        }

        $relasis = $query->orderBy('nama_fktp')->paginate(100)->withQueryString();
        $relasis->onEachSide(1);

        return view('admin.relasi-fktp-apotek.index', compact('relasis'));
    }

    public function create()
    {
        $mode = request('mode');
        
        if ($mode === 'apotek') {

            $fktps = RelasiFktpApotek::select('kode_fktp', 'nama_fktp')
                        ->distinct()
                        ->orderBy('nama_fktp')
                        ->get()
                        ->map(function($item) {
                            $item->kode_faskes = $item->kode_fktp;
                            $item->nama_faskes = $item->nama_fktp;
                            return $item;
                        });
            $apoteks = collect();
        } elseif ($mode === 'fktp') {
           
            $apoteks = RelasiFktpApotek::select('kode_apotek', 'nama_apotek')
                        ->distinct()
                        ->orderBy('nama_apotek')
                        ->get()
                        ->map(function($item) {
                            $item->kode_faskes = $item->kode_apotek;
                            $item->nama_faskes = $item->nama_apotek;
                            return $item;
                        });
            $fktps = collect();
        } else {
            $fktps = collect();
            $apoteks = collect();
        }
        
        return view('admin.relasi-fktp-apotek.create', compact('fktps', 'apoteks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:apotek,fktp',
        ]);

        if ($request->mode === 'apotek') {
            $request->validate([
                'nama_apotek' => 'required|string|max:255',
                'kode_apotek' => 'required|string|max:50',
                'fktps' => 'nullable|array',
                'fktps.*' => 'string',
                'fktps_manual' => 'nullable|string',
            ]);

            $fktps = $request->input('fktps', []);
            if (empty($fktps) && $request->filled('fktps_manual')) {
                $fktps = array_filter(array_map('trim', preg_split('/[\r\n,;]+/', $request->fktps_manual)));
            }

            if (empty($fktps)) {
                return redirect()->back()->withErrors(['fktps' => 'Silakan pilih atau masukkan setidaknya satu FKTP.'])->withInput();
            }

            $created = 0;
            foreach ($fktps as $nama_fktp) {
            
                $exists = RelasiFktpApotek::where('nama_fktp', $nama_fktp)
                                          ->where('nama_apotek', $request->nama_apotek)
                                          ->exists();
                if (!$exists) {
                   
                    $fktpData = RelasiFktpApotek::where('nama_fktp', $nama_fktp)->first();
                    $kodeFktp = $fktpData ? $fktpData->kode_fktp : '';
                    
                    if (!$kodeFktp) {
                       
                        $fktp = Faskes::where('nama_faskes', $nama_fktp)->first();
                        $kodeFktp = $fktp ? $fktp->kode_faskes : $nama_fktp;
                    }

                    RelasiFktpApotek::create([
                        'kode_fktp' => $kodeFktp,
                        'nama_fktp' => $nama_fktp,
                        'nama_apotek' => $request->nama_apotek,
                        'kode_apotek' => $request->kode_apotek,
                    ]);
                    $created++;
                }
            }

            return redirect()->route('admin.relasi-fktp-apotek.index')->with('success', "Berhasil menambah {$created} relasi untuk Apotek {$request->nama_apotek}.");

        } elseif ($request->mode === 'fktp') {
            $request->validate([
                'nama_fktp' => 'required|string|max:255',
                'kode_fktp' => 'required|string|max:50',
                'apoteks' => 'nullable|array',
                'apoteks.*' => 'string',
                'apoteks_manual' => 'nullable|string',
            ]);

            $apoteks = $request->input('apoteks', []);
            if (empty($apoteks) && $request->filled('apoteks_manual')) {
                $apoteks = array_filter(array_map('trim', preg_split('/[\r\n,;]+/', $request->apoteks_manual)));
            }

            if (empty($apoteks)) {
                return redirect()->back()->withErrors(['apoteks' => 'Silakan pilih atau masukkan setidaknya satu Apotek.'])->withInput();
            }

            $created = 0;
            foreach ($apoteks as $nama_apotek) {
            
                $exists = RelasiFktpApotek::where('kode_fktp', $request->kode_fktp)
                                          ->where('nama_apotek', $nama_apotek)
                                          ->exists();
                if (!$exists) {
                
                    $apotekData = RelasiFktpApotek::where('nama_apotek', $nama_apotek)->first();
                    $kodeApotek = $apotekData ? $apotekData->kode_apotek : '';
                    
                    if (!$kodeApotek) {
                      
                        $apotek = Faskes::where('nama_faskes', $nama_apotek)->first();
                        $kodeApotek = $apotek ? $apotek->kode_faskes : $nama_apotek;
                    }

                    RelasiFktpApotek::create([
                        'kode_fktp' => $request->kode_fktp,
                        'nama_fktp' => $request->nama_fktp,
                        'nama_apotek' => $nama_apotek,
                        'kode_apotek' => $kodeApotek,
                    ]);
                    $created++;
                }
            }

            return redirect()->route('admin.relasi-fktp-apotek.index')->with('success', "Berhasil menambah {$created} relasi untuk FKTP {$request->nama_fktp}.");
        }

        return redirect()->back()->withErrors(['mode' => 'Mode tidak valid.']);
    }

    public function show($id)
    {
        $relasi = RelasiFktpApotek::findOrFail($id);
        return view('admin.relasi-fktp-apotek.show', compact('relasi'));
    }

    public function edit($id)
    {
        $relasi = RelasiFktpApotek::findOrFail($id);
        $fktps = Faskes::whereHas('user', function($q) {
            $q->where('role', 'fktp');
        })->get();
        $apoteks = Faskes::whereHas('user', function($q) {
            $q->where('role', 'apotek');
        })->get();
        return view('admin.relasi-fktp-apotek.edit', compact('relasi', 'fktps', 'apoteks'));
    }

    public function update(Request $request, $id)
    {
        $relasi = RelasiFktpApotek::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kode_fktp' => 'required|string|max:50',
            'nama_fktp' => 'required|string|max:255',
            'nama_apotek' => 'required|string|max:255',
            'kode_apotek' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $relasi->update($request->all());

        return redirect()->route('admin.relasi-fktp-apotek.index')->with('success', 'Relasi FKTP-Apotek berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $relasi = RelasiFktpApotek::findOrFail($id);
        $relasi->delete();

        return redirect()->route('admin.relasi-fktp-apotek.index')->with('success', 'Relasi FKTP-Apotek berhasil dihapus.');
    }
}