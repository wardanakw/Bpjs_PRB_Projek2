<?php

namespace App\Exports;

use App\Models\DiagnosaPrb;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DiagnosaPrbExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = DiagnosaPrb::with('patient');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tgl_pelayanan', [$this->startDate, $this->endDate]);
        }

        return $query->get()->map(function ($item) {
            return [
                'ID Diagnosa' => $item->id_diagnosa,
                'No Kartu BPJS' => $item->patient->no_kartu_bpjs ?? '-',
                'Tanggal Pelayanan' => $item->tgl_pelayanan,
                'Diagnosa' => $item->diagnosa,
                'Status PRB' => $item->status_prb,
                'Kehadiran' => $item->kehadiran,
                'Catatan' => $item->catatan ?? '-',
            ];
        });
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
