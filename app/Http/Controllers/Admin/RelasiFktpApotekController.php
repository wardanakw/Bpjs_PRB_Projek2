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

            $fktpCodes = $request->input('fktps', []);
            if (empty($fktpCodes) && $request->filled('fktps_manual')) {
                $fktpCodes = array_filter(array_map('trim', preg_split('/[\r\n,;]+/', $request->fktps_manual)));
            }

            if (empty($fktpCodes)) {
                return redirect()->back()->withErrors(['fktps' => 'Silakan pilih atau masukkan setidaknya satu FKTP.'])->withInput();
            }

            $created = 0;
            foreach ($fktpCodes as $kodeFktp) {
                $exists = RelasiFktpApotek::where('kode_fktp', $kodeFktp)
                                          ->where('kode_apotek', $request->kode_apotek)
                                          ->exists();
                if (!$exists) {
                    $namaFktp = RelasiFktpApotek::where('kode_fktp', $kodeFktp)->value('nama_fktp');
                    if (!$namaFktp) {
                        $namaFktp = Faskes::where('kode_faskes', $kodeFktp)->value('nama_faskes') ?: $kodeFktp;
                    }

                    RelasiFktpApotek::create([
                        'kode_fktp' => $kodeFktp,
                        'nama_fktp' => $namaFktp,
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

            $apotekCodes = $request->input('apoteks', []);
            if (empty($apotekCodes) && $request->filled('apoteks_manual')) {
                $apotekCodes = array_filter(array_map('trim', preg_split('/[\r\n,;]+/', $request->apoteks_manual)));
            }

            if (empty($apotekCodes)) {
                return redirect()->back()->withErrors(['apoteks' => 'Silakan pilih atau masukkan setidaknya satu Apotek.'])->withInput();
            }

            $created = 0;
            foreach ($apotekCodes as $kodeApotek) {
                $exists = RelasiFktpApotek::where('kode_fktp', $request->kode_fktp)
                                          ->where('kode_apotek', $kodeApotek)
                                          ->exists();
                if (!$exists) {
                    $namaApotek = RelasiFktpApotek::where('kode_apotek', $kodeApotek)->value('nama_apotek');
                    if (!$namaApotek) {
                        $namaApotek = Faskes::where('kode_faskes', $kodeApotek)->value('nama_faskes') ?: $kodeApotek;
                    }

                    RelasiFktpApotek::create([
                        'kode_fktp' => $request->kode_fktp,
                        'nama_fktp' => $request->nama_fktp,
                        'nama_apotek' => $namaApotek,
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
            'kode_apotek' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $relasi->update($request->only(['kode_fktp', 'nama_fktp', 'nama_apotek', 'kode_apotek']));

        return redirect()->route('admin.relasi-fktp-apotek.index')->with('success', 'Relasi FKTP-Apotek berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $relasi = RelasiFktpApotek::findOrFail($id);
        $relasi->delete();

        return redirect()->route('admin.relasi-fktp-apotek.index')->with('success', 'Relasi FKTP-Apotek berhasil dihapus.');
    }
}