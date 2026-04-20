@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>
                        @if(request('mode') === 'apotek')
                            Tambah Apotek Baru dengan Mapping FKTP
                        @elseif(request('mode') === 'fktp')
                            Tambah FKTP Baru dengan Mapping Apotek
                        @else
                            Tambah Relasi
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.relasi-fktp-apotek.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="mode" value="{{ request('mode') }}">

                        @if(request('mode') === 'apotek')
                            <div class="mb-3">
                                <label for="nama_apotek" class="form-label">Nama Apotek</label>
                                <input type="text" class="form-control @error('nama_apotek') is-invalid @enderror" id="nama_apotek" name="nama_apotek" value="{{ old('nama_apotek') }}" required>
                                @error('nama_apotek')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="kode_apotek" class="form-label">Kode Apotek</label>
                                <input type="text" class="form-control @error('kode_apotek') is-invalid @enderror" id="kode_apotek" name="kode_apotek" value="{{ old('kode_apotek') }}" required>
                                @error('kode_apotek')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pilih FKTP yang Ter-mapping</label>
                                <input type="text" class="form-control mb-2" id="fktp-search" placeholder="Cari FKTP...">
                                <div class="border p-3" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm table-hover mb-0" id="fktp-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;"></th>
                                                <th>Nama FKTP</th>
                                                <th>Kode FKTP</th>
                                            </tr>
                                        </thead>
                                        <tbody id="fktp-table-body">
                                            @foreach($fktps as $fktp)
                                                <tr class="fktp-row" data-fktp-nama="{{ strtolower($fktp->nama_faskes) }}" data-fktp-kode="{{ strtolower($fktp->kode_faskes) }}">
                                                    <td>
                                                        <input class="form-check-input fktp-checkbox" type="checkbox" name="fktps[]" value="{{ $fktp->kode_faskes }}" 
                                                               id="fktp_{{ $fktp->id }}"
                                                               data-nama="{{ $fktp->nama_faskes }}"
                                                               data-kode="{{ $fktp->kode_faskes }}"
                                                               {{ in_array($fktp->kode_faskes, old('fktps', [])) ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <label for="fktp_{{ $fktp->id }}" class="form-check-label" style="cursor: pointer;">
                                                            {{ $fktp->nama_faskes }}
                                                        </label>
                                                    </td>
                                                    <td>{{ $fktp->kode_faskes }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @error('fktps')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="fktps_manual" class="form-label">Atau masukkan FKTP secara manual (pisah baris, koma, atau titik koma)</label>
                                <textarea class="form-control @error('fktps_manual') is-invalid @enderror" id="fktps_manual" name="fktps_manual" rows="3">{{ old('fktps_manual') }}</textarea>
                                @error('fktps_manual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Preview FKTP Terpilih</label>
                                <div id="fktp-preview" class="border p-3 bg-light">
                                    <p class="text-muted">Belum ada FKTP yang dipilih</p>
                                </div>
                            </div>
                        @elseif(request('mode') === 'fktp')
                            <div class="mb-3">
                                <label for="nama_fktp" class="form-label">Nama FKTP</label>
                                <input type="text" class="form-control @error('nama_fktp') is-invalid @enderror" id="nama_fktp" name="nama_fktp" value="{{ old('nama_fktp') }}" required>
                                @error('nama_fktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="kode_fktp" class="form-label">Kode FKTP</label>
                                <input type="text" class="form-control @error('kode_fktp') is-invalid @enderror" id="kode_fktp" name="kode_fktp" value="{{ old('kode_fktp') }}" required>
                                @error('kode_fktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pilih Apotek yang Ter-mapping</label>
                                <input type="text" class="form-control mb-2" id="apotek-search" placeholder="Cari Apotek...">
                                <div class="border p-3" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm table-hover mb-0" id="apotek-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;"></th>
                                                <th>Nama Apotek</th>
                                                <th>Kode Apotek</th>
                                            </tr>
                                        </thead>
                                        <tbody id="apotek-table-body">
                                            @foreach($apoteks as $apotek)
                                                <tr class="apotek-row" data-apotek-nama="{{ strtolower($apotek->nama_faskes) }}" data-apotek-kode="{{ strtolower($apotek->kode_faskes) }}">
                                                    <td>
                                                        <input class="form-check-input apotek-checkbox" type="checkbox" name="apoteks[]" value="{{ $apotek->kode_faskes }}" 
                                                               id="apotek_{{ $apotek->id }}"
                                                               data-nama="{{ $apotek->nama_faskes }}"
                                                               data-kode="{{ $apotek->kode_faskes }}"
                                                               {{ in_array($apotek->kode_faskes, old('apoteks', [])) ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <label for="apotek_{{ $apotek->id }}" class="form-check-label" style="cursor: pointer;">
                                                            {{ $apotek->nama_faskes }}
                                                        </label>
                                                    </td>
                                                    <td>{{ $apotek->kode_faskes }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @error('apoteks')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="apoteks_manual" class="form-label">Atau masukkan Apotek secara manual (pisah baris, koma, atau titik koma)</label>
                                <textarea class="form-control @error('apoteks_manual') is-invalid @enderror" id="apoteks_manual" name="apoteks_manual" rows="3">{{ old('apoteks_manual') }}</textarea>
                                @error('apoteks_manual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Preview Apotek Terpilih</label>
                                <div id="apotek-preview" class="border p-3 bg-light">
                                    <p class="text-muted">Belum ada Apotek yang dipilih</p>
                                </div>
                            </div>
                        @else
                            <p>Mode tidak valid. <a href="{{ route('admin.relasi-fktp-apotek.index') }}">Kembali</a></p>
                        @endif

                        @if(request('mode') === 'apotek' || request('mode') === 'fktp')
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.relasi-fktp-apotek.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fktpCheckboxes = document.querySelectorAll('.fktp-checkbox');
    const apotekCheckboxes = document.querySelectorAll('.apotek-checkbox');
    const fktpPreview = document.getElementById('fktp-preview');
    const apotekPreview = document.getElementById('apotek-preview');
    const fktpSearchInput = document.getElementById('fktp-search');
    const apotekSearchInput = document.getElementById('apotek-search');

    // Search FKTP
    if (fktpSearchInput) {
        fktpSearchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const fktpRows = document.querySelectorAll('.fktp-row');
            
            fktpRows.forEach(row => {
                const nama = row.dataset.fktpNama || '';
                const kode = row.dataset.fktpKode || '';
                
                if (nama.includes(searchTerm) || kode.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Search Apotek
    if (apotekSearchInput) {
        apotekSearchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const apotekRows = document.querySelectorAll('.apotek-row');
            
            apotekRows.forEach(row => {
                const nama = row.dataset.apotekNama || '';
                const kode = row.dataset.apotekKode || '';
                
                if (nama.includes(searchTerm) || kode.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    function updateFktpPreview() {
        const selectedFktps = Array.from(fktpCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => ({
                nama: cb.dataset.nama,
                kode: cb.dataset.kode
            }));

        if (selectedFktps.length === 0) {
            fktpPreview.innerHTML = '<p class="text-muted">Belum ada FKTP yang dipilih</p>';
        } else {
            let html = '<table class="table table-sm mb-0"><thead><tr><th>Nama FKTP</th><th>Kode FKTP</th></tr></thead><tbody>';
            selectedFktps.forEach(fktp => {
                html += `<tr><td>${fktp.nama}</td><td>${fktp.kode}</td></tr>`;
            });
            html += '</tbody></table>';
            fktpPreview.innerHTML = html;
        }
    }

    function updateApotekPreview() {
        const selectedApoteks = Array.from(apotekCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => ({
                nama: cb.dataset.nama,
                kode: cb.dataset.kode
            }));

        if (selectedApoteks.length === 0) {
            apotekPreview.innerHTML = '<p class="text-muted">Belum ada Apotek yang dipilih</p>';
        } else {
            let html = '<table class="table table-sm mb-0"><thead><tr><th>Nama Apotek</th><th>Kode Apotek</th></tr></thead><tbody>';
            selectedApoteks.forEach(apotek => {
                html += `<tr><td>${apotek.nama}</td><td>${apotek.kode}</td></tr>`;
            });
            html += '</tbody></table>';
            apotekPreview.innerHTML = html;
        }
    }

    fktpCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateFktpPreview);
    });

    apotekCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateApotekPreview);
    });

    // Initialize preview on page load
    if (fktpPreview) updateFktpPreview();
    if (apotekPreview) updateApotekPreview();
});
</script>
@endsection