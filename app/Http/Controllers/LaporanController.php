<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiagnosaPrb;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DiagnosaPrbExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $currentUser = Auth::user();

            if (!$currentUser) {
                return redirect()->route('login')->with('error', 'Unauthorized');
            }

            $query = DiagnosaPrb::query()
                ->with('patient', 'obatPrb')
                ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien');

            if ($currentUser->role !== 'admin') {
                if ($currentUser->role === 'fktp') {
                    $query->where('patients.fktp_kode', $currentUser->fktp_kode);
                } elseif ($currentUser->role === 'rumah_sakit') {
                    $query->where('patients.created_by', $currentUser->id_user);
                } elseif ($currentUser->role === 'apotek') {
                    // Apotek bisa melihat semua data
                }
            }

            if ($startDate && $endDate) {
                $query->whereBetween('diagnosa_prb.tgl_pelayanan', [$startDate, $endDate]);
            }

            $diagnosas = $query->select('diagnosa_prb.*')
                ->orderBy('diagnosa_prb.tgl_pelayanan', 'desc')
                ->paginate(10);

            return view('exports.laporan', compact('diagnosas', 'startDate', 'endDate'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data laporan. Silakan coba lagi.');
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $currentUser = Auth::user();

            if (!$currentUser) {
                return redirect()->route('login')->with('error', 'Unauthorized');
            }

            $filterValue = null;
            if ($currentUser->role === 'fktp') {
                $filterValue = $currentUser->fktp_kode;
            } elseif ($currentUser->role === 'rumah_sakit') {
                $filterValue = $currentUser->id_user;
            }

            return Excel::download(
                new DiagnosaPrbExport($startDate, $endDate, $filterValue, $currentUser->role),
                'Laporan_Data_PRB_' . date('Y-m-d_H-i-s') . '.xlsx'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengexport data. Silakan coba lagi.');
        }
    }

    public function downloadPdf($filename)
    {
        try {
            $user = auth()->user();
            if (!$user || !in_array($user->role, ['admin', 'rumah_sakit'])) {
                abort(403, 'Unauthorized');
            }

            $filename = basename(urldecode($filename));
            
            if (Storage::disk('public')->exists('diagnosa/' . $filename)) {
                return Storage::disk('public')->download('diagnosa/' . $filename);
            }
            
            
            if (Storage::disk('private')->exists('diagnosa/' . $filename)) {
                return Storage::disk('private')->download('diagnosa/' . $filename);
            }

            abort(404, 'File not found');
        } catch (\Exception $e) {
            if ($e->getStatusCode() === 404 || $e->getStatusCode() === 403) {
                throw $e;
            }
            abort(500, 'Error downloading file');
        }
    }

}
