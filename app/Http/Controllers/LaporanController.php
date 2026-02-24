<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiagnosaPrb;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DiagnosaPrbExport;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $currentUser = Auth::user();

        $query = DiagnosaPrb::query()
            ->with('patient', 'obatPrb')
            ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien');

        if ($currentUser->role !== 'admin') {
            if ($currentUser->role === 'fktp') {
                $query->where('patients.fktp_kode', $currentUser->fktp_kode);
            } elseif ($currentUser->role === 'rumah_sakit') {
                $query->where('patients.created_by', $currentUser->id_user);
            }
        }

        if ($startDate && $endDate) {
            $query->whereBetween('diagnosa_prb.tgl_pelayanan', [$startDate, $endDate]);
        }

        $diagnosas = $query->select('diagnosa_prb.*')
            ->orderBy('diagnosa_prb.tgl_pelayanan', 'desc')
            ->paginate(10);

        return view('exports.laporan', compact('diagnosas', 'startDate', 'endDate'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $currentUser = Auth::user();

        $filterValue = null;
        if ($currentUser->role === 'fktp') {
            $filterValue = $currentUser->fktp_kode;
        } elseif ($currentUser->role === 'rumah_sakit') {
            $filterValue = $currentUser->id_user;
        }

        return Excel::download(new DiagnosaPrbExport($startDate, $endDate, $filterValue, $currentUser->role), 'Laporan_Data_PRB.xlsx');
    }

    public function downloadPdf($filename)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'rumah_sakit'])) {
            abort(403, 'Unauthorized');
        }

        $filename = urldecode($filename);
        $path = 'diagnosa/' . $filename;
        \Log::info('Download PDF path: ' . $path . ', exists: ' . (\Storage::disk('private')->exists($path) ? 'yes' : 'no'));
        if (!\Storage::disk('private')->exists($path)) {
            abort(404, 'File not found');
        }

        return \Storage::disk('private')->download($path);
    }

}
