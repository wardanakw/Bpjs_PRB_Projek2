    @extends('layouts.app')

    @section('title', 'Laporan Data PRB')

    @section('content')
    <style>
    * { font-family: 'Poppins', sans-serif; }
    body { background-color: #f5f7fa; }

    .main-content {
        position: relative;
        top: -30px;
        left: -40px;
        padding-right: 15px;
    }

    .filter-box {
        background: linear-gradient(90deg, #0078D7, #41b7eaff);
        border-radius: 15px;
        padding: 20px 30px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 25px;
        width: calc(100% + 80px);
    }

    .left-section {
        display: flex;
        align-items: center;
        gap: 25px;
        flex-wrap: wrap;
    }

    .date-group label {
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
    }

    .date-group input[type="date"] {
        border: none;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 14px;
        color: #333;
        width: 180px;
    }

    .btn-tampilkan {
        background: white;
        color: #0078D7;
        border: none;
        padding: 8px 25px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-tampilkan:hover {
        background: #e9f4ff;
        color: #0056b3;
    }

    .btn-export {
        background: #28a745;
        border: none;
        color: white;
        padding: 8px 25px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-export:hover {
        background: #1f8a3b;
    }

    .table-container {
        width: calc(100% + 80px);
    }

    .table {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .table thead {
        background: #f0f3f7;
        font-weight: 600;
        color: #333;
    }

    .table tbody tr {
        transition: all 0.2s;
    }

    .table tbody tr:hover {
        background: #f9fbff;
    }

    .table td, .table th {
        vertical-align: middle;
        border-color: #e0e6ef;
        padding: 10px 15px;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #fafcff;
    }

    .table-bordered {
        border: 1px solid #e0e6ef;
    }

    .badge-aktif {
        background-color: #11871bff;
        color: white;
    }

    .badge-nonaktif {
        background-color: #b0b0b0;
        color: white;
    }


    @media (max-width: 768px) {
        .filter-box {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }
        .left-section {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }
        .date-group input {
            width: 100%;
        }
    }
    </style>

    <div class="main-content">

        <div class="filter-box">
            <div class="left-section">
                <div class="date-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="start_date" id="startDate" value="{{ $startDate }}">
                </div>
                <div class="date-group">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="end_date" id="endDate" value="{{ $endDate }}">
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn-tampilkan"
                    onclick="window.location.href='{{ route('exports.laporan') }}?start_date=' + document.getElementById('startDate').value + '&end_date=' + document.getElementById('endDate').value;">
                    Tampilkan
                </button>

                <button type="button" class="btn-export"
                    onclick="window.location.href='{{ route('laporan.export') }}?start_date=' + document.getElementById('startDate').value + '&end_date=' + document.getElementById('endDate').value;">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </button>
            </div>
        </div>

        <div class="table-container">
            <h5 class="fw-bold mb-3">Laporan Data PRB</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID Diagnosa</th>
                            <th>No Kartu BPJS</th>
                            <th>Tanggal Pelayanan</th>
                            <th>Diagnosa</th>
                            <th>Status PRB</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($diagnosas as $d)
                            <tr>
                                <td>{{ $d->id_diagnosa }}</td>
                                <td>{{ $d->patient->no_kartu_bpjs?? '-' }}</td>
                                <td>{{ $d->tgl_pelayanan ? \Carbon\Carbon::parse($d->tgl_pelayanan)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $d->diagnosa }}</td>
                                <td>
                                    <span class="badge {{ $d->status_prb == 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                                    {{ $d->status_prb }}
                                </span>
                                </td>
                                <td>{{ $d->catatan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @endsection
