@extends('layouts.app')

@section('title', 'Detail Pasien')

@section('content')
<style>
body {
    background-color: #f7faff;
    font-family: 'Poppins', sans-serif;
}

.card {
    border: none;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.profile-img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: #e9f2ff;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 48px;
    color: #0078D7;
}

.badge-status {
    font-size: 13px;
    border-radius: 10px;
    padding: 6px 12px;
}

.nav-tabs {
    border-bottom: 2px solid #e8eef5;
}
.nav-tabs .nav-link {
    color: #6c757d;
    font-weight: 500;
    border: none;
    border-bottom: 3px solid transparent;
    background: transparent;
}
.nav-tabs .nav-link.active {
    color: #0078D7;
    border-bottom: 3px solid #0078D7;
}

.btn-outline-primary {
    border-radius: 8px;
    border: 1.5px solid #0078D7;
    color: #0078D7;
    font-weight: 500;
}
.btn-outline-primary:hover {
    background: #0078D7;
    color: #fff;
}


.table thead {
    background-color: #f8f9fa;
    font-weight: 600;
}
.table td, .table th { vertical-align: middle; }
.table-responsive .table { width: 100%; }

.btn-primary {
    background-color: #0078D7;
    border: none;
    border-radius: 8px;
    color: white;
}

.row .col-md-9 h6.fw-bold {
    color: #0078D7;
}

.row .col-md-6 p {
    display: grid;
    grid-template-columns: 150px auto;
    align-items: center;
    margin-bottom: 6px;
}
.row .col-md-6 p strong {
    text-align: left;
    position: relative;
    padding-right: 10px;
}
.row .col-md-6 p strong::after {
    content: " :";
    position: absolute;
    right: 0;
}

.back-arrow {
    font-size: 28px;
    color: #0078D7;
    text-decoration: none;
    margin-bottom: 5px;
    display: inline-block;
}
.back-arrow:hover {
    color: #000000ff;
}

/* Pagination Styling */
.pagination {
    margin: 20px 0;
}
.pagination .page-link {
    color: #0078D7;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px !important;
    margin: 0 2px;
    padding: 8px 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.pagination .page-link:hover {
    color: #005ea6;
    background-color: #f8f9fa;
    border-color: #0078D7;
}
.pagination .page-item.active .page-link {
    background-color: #0078D7;
    border-color: #0078D7;
    color: white;
    box-shadow: 0 2px 4px rgba(0,120,215,0.3);
}
.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

</style>

<div class="container mt-4">

 <div class="mb-3">
       <a href="javascript:history.back()" class="back-arrow">
  <i class="bi bi-arrow-90deg-left"></i>
</a>
    </div>
    <div class="card mb-4 p-4 shadow-sm rounded-4">
        <div class="row align-items-center">
            
            <div class="col-md-3 text-center border-end">
                <div class="profile-img mx-auto mb-2">
                    <i class="bi bi-person"></i>
                </div>
                <h5 class="fw-bold">{{ $pasien->nama_pasien }}</h5>

                @if($pasien->diagnosaPrb->first()?->status_prb == 'Aktif')
                    <span class="badge bg-success badge-status">Aktif</span>
                @else
                    <span class="badge bg-secondary badge-status">Tidak Aktif</span>
                @endif
            </div>

            
            <div class="col-md-9 ps-4">
                <h6 class="fw-bold mb-3 text-primary">Informasi Pasien</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>No Kartu BPJS </strong> {{ $pasien->no_kartu_bpjs }}</p>
                        <p><strong>Tanggal Lahir </strong> {{ $pasien->tanggal_lahir ?? '-' }}</p>
                        <p><strong>FKTP Asal </strong> {{ $pasien->fktp_asal }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Nama Lengkap </strong> {{ $pasien->nama_pasien }}</p>
                        <p><strong>No Telp WA </strong> {{ $pasien->no_telp }}</p>
                        <p><strong>No Kunjungan </strong> {{ $pasien->no_kunjungan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card p-4 shadow-sm rounded-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <ul class="nav nav-tabs border-0" id="prbTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button">
                        PRB Aktif
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button">
                        Riwayat PRB
                    </button>
                </li>
            </ul>
            <button class="btn btn-outline-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Data
            </button>
        </div>

        <div class="tab-content mt-3">
           
            <div class="tab-pane fade show active" id="aktif">
                @if($prbAktif->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle text-center">
                            <thead>
                                <tr>
                                    <th>No PRB</th>
                                    <th>Tanggal Pemeriksaan</th>
                                    <th>Diagnosa</th>
                                    <th>No PIC</th>
                                    <th>Status PRB</th>
                                    <th>Dosis Obat</th>
                                    <th>File</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prbAktif as $diagnosa)
                                    <tr>
                                        <td>{{ $diagnosa->id_diagnosa }}</td>
                                        <td>{{ \Carbon\Carbon::parse($diagnosa->tgl_pelayanan)->format('d M Y') }}</td>
                                        <td>{{ $diagnosa->diagnosa }}</td>
                                        <td>{{ $diagnosa->no_telp_pic ?? '-' }}</td>
                                        <td><span class="badge bg-success">{{ $diagnosa->status_prb }}</span></td>
                                              <td class="text-start">
    @if($diagnosa->obatPrb && $diagnosa->obatPrb->count())
        <ul class="list-unstyled mb-0">
            @php
                // Filter obat yang belum diklaim (untuk tampilan)
                $obatBelumKlaim = auth()->user()->role === 'apotek' 
                    ? $diagnosa->obatPrb->where('is_klaim', false) 
                    : $diagnosa->obatPrb;
            @endphp
            @foreach($obatBelumKlaim as $obat)
                <li class="mb-2">
                    <strong>{{ $obat->nama_obat }}</strong><br>
                    Jumlah: {{ $obat->jumlah_obat ?? '-' }} 
                    {{ $obat->satuan ?? 'tablet' }}<br>
                    Dosis: {{ $obat->dosis_obat ?? '-' }}<br>
                    Aturan Pakai: {{ $obat->aturan_pakai ?? '-' }}
                </li>
            @endforeach
        </ul>
    @else
        <span class="text-muted">Belum ada obat untuk diagnosa ini</span>
    @endif
</td>
                                        <td>
                                            @if($diagnosa->file_upload)
                                                <a href="{{ route('pasien.diagnosa.file', $diagnosa->id_diagnosa) }}" target="_blank" class="btn btn-sm btn-outline-info">Lihat</a>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(auth()->user()->role === 'apotek')
                                                {{-- Jika belum ada file PDF bukti bayar untuk diagnosa ini, tampilkan tombol untuk membuka modal upload --}}
                                                @if(empty($diagnosa->obatPrb->first()->bukti_bayar_pdf))
                                                    <button type="button" class="btn btn-sm btn-warning openUploadModal" data-diagnosa-id="{{ $diagnosa->id_diagnosa }}" data-pasien-name="{{ $pasien->nama_pasien }}">
                                                        <i class="bi bi-upload"></i> Upload PDF
                                                    </button>
                                                @else
                                                    {{-- Sudah ada file, tampilkan tombol Klaim jika ada obat belum diklaim --}}
                                                    @if($diagnosa->obatPrb && $diagnosa->obatPrb->where('is_klaim', false)->count() > 0)
                                                        <form action="{{ route('apotek.diagnosa.klaim', $diagnosa->id_diagnosa) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="bi bi-check-circle"></i> Klaim Semua Obat
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-secondary">Sudah Diklaim</span>
                                                    @endif
                                                @endif
                                            @else
                                                <a href="{{ route('pasien.edit', $pasien->id_pasien) }}" class="btn btn-sm btn-primary">Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $prbAktif->links() }}
                        </div>
                    </div>
                @else
                    <p class="text-muted">Belum ada PRB aktif.</p>
                @endif
            </div>

            <div class="tab-pane fade" id="riwayat">
                @if($prbRiwayat->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle text-center">
                            <thead>
                                <tr>
                                    <th>No PRB</th>
                                    <th>Tanggal Pemeriksaan</th>
                                    <th>Diagnosa</th>
                                    <th>No PIC</th>
                                    <th>Status PRB</th>
                                    <th>Dosis Obat</th>
                                    <th>File</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prbRiwayat as $diagnosa)
                                    <tr>
                                        <td>{{ $diagnosa->id_diagnosa }}</td>
                                        <td>{{ \Carbon\Carbon::parse($diagnosa->tgl_pelayanan)->format('d M Y') }}</td>
                                        <td>{{ $diagnosa->diagnosa }}</td>
                                        <td>{{ $diagnosa->no_telp_pic ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $diagnosa->status_prb == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $diagnosa->status_prb }}
                                            </span>
                                        </td>
                                              <td class="text-start">
    @if($diagnosa->obatPrb && $diagnosa->obatPrb->count())
        <ul class="list-unstyled mb-0">
            @foreach($diagnosa->obatPrb as $obat)
                <li class="mb-2">
                    <strong>{{ $obat->nama_obat }}</strong><br>
                    Jumlah: {{ $obat->jumlah_obat ?? '-' }} 
                    {{ $obat->satuan ?? 'tablet' }}<br>
                    Dosis: {{ $obat->dosis_obat ?? '-' }}<br>
                    Aturan Pakai: {{ $obat->aturan_pakai ?? '-' }}
                </li>
            @endforeach
        </ul>
    @else
        <span class="text-muted">Belum ada obat untuk diagnosa ini</span>
    @endif
</td>
                              <td>
    @if($diagnosa->file_upload)
        <a href="{{ route('pasien.diagnosa.file', $diagnosa->id_diagnosa) }}" target="_blank" class="btn btn-sm btn-outline-info">
            <i class="bi bi-eye"></i> Lihat
        </a>
    @else
        <span class="text-muted">Tidak ada</span>
    @endif
</td>
                                        <td>
                                            <a href="{{ route('pasien.edit', $pasien->id_pasien) }}" class="btn btn-sm btn-primary">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $prbRiwayat->links() }}
                        </div>
                    </div>
                @else
                    <p class="text-muted">Belum ada riwayat PRB.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
