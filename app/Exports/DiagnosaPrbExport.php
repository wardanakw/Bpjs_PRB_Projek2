<?php

namespace App\Exports;

use App\Models\DiagnosaPrb;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DiagnosaPrbExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;
    protected $ftpKode;
    protected $userRole;

    public function __construct($startDate = null, $endDate = null, $ftpKode = null, $userRole = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->ftpKode = $ftpKode;
        $this->userRole = $userRole;
    }

    public function collection()
    {
        try {
            $query = DiagnosaPrb::query()
                ->with('patient')
                ->join('patients', 'diagnosa_prb.id_pasien', '=', 'patients.id_pasien');

            if ($this->userRole !== 'admin') {
                if ($this->userRole === 'fktp') {
                    $query->where('patients.fktp_kode', $this->ftpKode);
                } elseif ($this->userRole === 'rumah_sakit') {
                    $query->where('patients.created_by', $this->ftpKode);
                } elseif ($this->userRole === 'apotek') {
                    // Apotek bisa akses semua
                }
            }

            if ($this->startDate && $this->endDate) {
                $query->whereBetween('diagnosa_prb.tgl_pelayanan', [$this->startDate, $this->endDate]);
            }

            return $query->select('diagnosa_prb.*')
                ->orderBy('diagnosa_prb.tgl_pelayanan', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'ID Diagnosa' => $item->id_diagnosa ?? '-',
                        'No Kartu BPJS' => $item->patient->no_kartu_bpjs ?? '-',
                        'Tanggal Pelayanan' => $item->tgl_pelayanan ?? '-',
                        'Diagnosa' => $item->diagnosa ?? '-',
                        'Status PRB' => $item->status_prb ?? '-',
                        'Kehadiran' => $item->kehadiran ?? '-',
                        'Catatan' => $item->catatan ?? '-',
                    ];
                });
        } catch (\Exception $e) {
            return collect([]); // Return empty collection jika error
        }
    }

    public function headings(): array
    {
        return [
            'ID Diagnosa',
            'No Kartu BPJS',
            'Tanggal Pelayanan',
            'Diagnosa',
            'Status PRB',
            'Kehadiran',
            'Catatan',
        ];
    }
}
