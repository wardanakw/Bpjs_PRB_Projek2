<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiagnosaPrb;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DiagnosaPrbExport;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = DiagnosaPrb::with('patient', 'obatPrb');

        if ($startDate && $endDate) {
            $query->whereBetween('tgl_pelayanan', [$startDate, $endDate]);
        }

        $diagnosas = $query->orderBy('tgl_pelayanan', 'desc')->get();

        return view('exports.laporan', compact('diagnosas', 'startDate', 'endDate'));
    }

   public function exportExcel(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    return Excel::download(new DiagnosaPrbExport($startDate, $endDate), 'Laporan_Data_PRB.xlsx');
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
