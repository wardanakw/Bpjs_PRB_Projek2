<div class="modal fade" id="createFaskesModal" tabindex="-1" aria-labelledby="createFaskesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title" id="createFaskesModalLabel">Tambah Fasilitas Kesehatan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.faskes.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nama User</label>
              <input type="text" name="name" class="form-control" required placeholder="Nama lengkap user">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Role Faskes</label>
          <select name="role" id="role" class="form-select" required>
  <option value="">-- Pilih Role --</option>
  <option value="fktp">FKTP</option>
  <option value="rumah_sakit">Rumah Sakit</option>
  <option value="apotek">Apotek</option>
</select>

            </div>
<div class="col-md-6" id="row_kode_faskes">
    <label class="form-label fw-semibold">Kode Faskes</label>
    <input type="text" name="kode_faskes" class="form-control" id="kode_faskes" placeholder="Contoh: RS001">
</div>

      <div class="mb-3" id="row_kode_apotek" style="display:none;">
    <label>Kode Apotek</label>
    <select name="kode_apotek" id="kode_apotek" class="form-control">
        <option value="">-- Pilih Kode Apotek --</option>
        @foreach($relasi as $row)
            <option value="{{ $row->kode_apotek }}">{{ $row->kode_apotek }} - {{ $row->nama_apotek }}</option>
        @endforeach
    </select>
</div>


            <div class="col-md-6">
              <label class="form-label fw-semibold">Nama Faskes</label>
              <input type="text" name="nama_faskes" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Jenis Faskes</label>
              <input type="text" name="jenis_faskes" class="form-control" required>
            </div>

            <div class="col-md-12">
              <label class="form-label fw-semibold">Alamat Faskes</label>
              <input type="text" name="alamat_faskes" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Kecamatan</label>
              <input type="text" name="kecamatan" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Kabupaten / Kota</label>
              <input type="text" name="kabupaten" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Provinsi</label>
              <input type="text" name="provinsi" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Kode Pos</label>
              <input type="text" name="kode_pos" class="form-control">
            </div>

            <hr class="mt-4 mb-2">

            <div class="col-md-6">
              <label class="form-label fw-semibold">Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function () {
    const role = this.value;

    const rowKodeFaskes = document.getElementById('row_kode_faskes');
    const rowKodeApotek = document.getElementById('row_kode_apotek');

    const inputFaskes = document.getElementById('kode_faskes');
    const inputApotek = document.getElementById('kode_apotek');

    if (role === 'fktp') {

        rowKodeFaskes.style.display = 'block';
        inputFaskes.removeAttribute('readonly');
        inputFaskes.setAttribute('required', true);

        rowKodeApotek.style.display = 'none';
        inputApotek.removeAttribute('required');
        inputApotek.value = "";

    } else if (role === 'apotek') {
        rowKodeFaskes.style.display = 'none';
        inputFaskes.removeAttribute('required');
        inputFaskes.value = "";

        rowKodeApotek.style.display = 'block';
        inputApotek.setAttribute('required', true);

    } else {
        rowKodeFaskes.style.display = 'none';
        rowKodeApotek.style.display = 'none';

        inputFaskes.removeAttribute('required');
        inputApotek.removeAttribute('required');

        inputFaskes.value = "";
        inputApotek.value = "";
    }
});
</script>

