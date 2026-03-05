<div class="modal fade" id="editFaskesModal" tabindex="-1" aria-labelledby="editFaskesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title" id="editFaskesModalLabel">Edit Fasilitas Kesehatan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" id="editFaskesForm">
        @csrf
        @method('PUT')
        
        <input type="hidden" id="role_hidden" name="role" value="">
      
        <div class="modal-body">
          <div class="row g-3">
            
            {{-- Nama User --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nama User <span class="text-danger">*</span></label>
              <input type="text" name="name" id="edit_name" class="form-control @error('name') is-invalid @enderror" 
                     required placeholder="Nama lengkap user">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Role --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Role Faskes</label>
              <input type="text" id="edit_role_display" class="form-control" disabled>
            </div>

            {{-- Kode Faskes (untuk non-apotek) --}}
            <div class="col-md-6" id="edit-kode-faskes-container">
              <label class="form-label fw-semibold">Kode Faskes</label>
              <input type="text" id="edit_kode_faskes" name="kode_faskes" class="form-control">
              <small class="text-muted">Untuk FKTP dan Rumah Sakit</small>
            </div>

            {{-- Kode Apotek (untuk apotek) --}}
            <div class="col-md-6" id="edit-kode-apotek-container" style="display: none;">
              <label class="form-label fw-semibold">Kode Apotek <span class="text-danger">*</span></label>
              <select id="edit_kode_apotek" name="kode_apotek" class="form-select">
                <option value="">-- Pilih Kode Apotek --</option>
                @foreach($relasi as $row)
                  <option value="{{ $row->kode_apotek }}">
                    {{ $row->kode_apotek }} - {{ $row->nama_apotek }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">Kode apotek dari relasi FKTP</small>
            </div>

            {{-- Nama Faskes --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nama Faskes <span class="text-danger">*</span></label>
              <input type="text" name="nama_faskes" id="edit_nama_faskes" class="form-control @error('nama_faskes') is-invalid @enderror" 
                     required>
              @error('nama_faskes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Jenis Faskes --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Jenis Faskes <span class="text-danger">*</span></label>
              <input type="text" name="jenis_faskes" id="edit_jenis_faskes" class="form-control @error('jenis_faskes') is-invalid @enderror" 
                     required>
              @error('jenis_faskes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Alamat Faskes --}}
            <div class="col-md-12">
              <label class="form-label fw-semibold">Alamat Faskes <span class="text-danger">*</span></label>
              <input type="text" name="alamat_faskes" id="edit_alamat_faskes" class="form-control @error('alamat_faskes') is-invalid @enderror" 
                     required>
              @error('alamat_faskes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Kecamatan --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kecamatan</label>
              <input type="text" name="kecamatan" id="edit_kecamatan" class="form-control @error('kecamatan') is-invalid @enderror">
              @error('kecamatan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Kabupaten/Kota --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kabupaten/Kota</label>
              <input type="text" name="kabupaten" id="edit_kabupaten" class="form-control @error('kabupaten') is-invalid @enderror">
              @error('kabupaten')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Provinsi --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Provinsi</label>
              <input type="text" name="provinsi" id="edit_provinsi" class="form-control @error('provinsi') is-invalid @enderror">
              @error('provinsi')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Kode Pos --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kode Pos</label>
              <input type="text" name="kode_pos" id="edit_kode_pos" class="form-control @error('kode_pos') is-invalid @enderror" 
                     pattern="[0-9]*">
              @error('kode_pos')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Nomor PIC --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nomor PIC</label>
              <input type="tel" name="nomor_pic" id="edit_nomor_pic" class="form-control @error('nomor_pic') is-invalid @enderror" 
                     placeholder="Contoh: 081234567890">
              @error('nomor_pic')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <hr class="mt-4 mb-2">

            {{-- Username --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" name="username" id="edit_username" class="form-control @error('username') is-invalid @enderror" 
                     required>
              @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Password --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Password (Kosongkan jika tidak ingin mengubah)</label>
              <input type="password" name="password" id="edit_password" class="form-control @error('password') is-invalid @enderror" 
                     placeholder="Password baru (opsional)">
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="submitEditBtn">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const editModal = document.getElementById('editFaskesModal');
    const editForm = document.getElementById('editFaskesForm');
    const editKodeApotekContainer = document.getElementById('edit-kode-apotek-container');
    const editKodeFaskesContainer = document.getElementById('edit-kode-faskes-container');
    const editKodeApotek = document.getElementById('edit_kode_apotek');
    const editKodeFaskes = document.getElementById('edit_kode_faskes');
    const roleHidden = document.getElementById('role_hidden');
    
    document.querySelectorAll('[data-edit-faskes]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const faskesData = JSON.parse(this.getAttribute('data-edit-faskes'));

            editForm.action = `/admin/faskes/${faskesData.id}`;
            document.getElementById('edit_name').value = faskesData.user_name;
            document.getElementById('edit_role_display').value = faskesData.role;
            document.getElementById('edit_nama_faskes').value = faskesData.nama_faskes;
            document.getElementById('edit_jenis_faskes').value = faskesData.jenis_faskes;
            document.getElementById('edit_alamat_faskes').value = faskesData.alamat_faskes;
            document.getElementById('edit_kecamatan').value = faskesData.kecamatan || '';
            document.getElementById('edit_kabupaten').value = faskesData.kabupaten || '';
            document.getElementById('edit_provinsi').value = faskesData.provinsi || '';
            document.getElementById('edit_kode_pos').value = faskesData.kode_pos || '';
            document.getElementById('edit_nomor_pic').value = faskesData.nomor_pic || '';
            document.getElementById('edit_username').value = faskesData.username;
            document.getElementById('edit_password').value = '';

            roleHidden.value = faskesData.role;

            if (faskesData.role === 'apotek') {

                editKodeFaskesContainer.style.display = 'none';
                editKodeApotekContainer.style.display = 'block';

                editKodeFaskes.disabled = true;
                editKodeApotek.disabled = false;

                editKodeApotek.value = faskesData.kode_apotek || '';

            } else {

                editKodeFaskesContainer.style.display = 'block';
                editKodeApotekContainer.style.display = 'none';

                editKodeFaskes.disabled = false;
                editKodeApotek.disabled = true;

                editKodeFaskes.value = faskesData.kode_faskes || '';
            }

            const modal = new bootstrap.Modal(editModal);
            modal.show();
        });
    });

    editModal.addEventListener('hidden.bs.modal', function() {
        editForm.reset();
        editKodeApotek.disabled = false;
        editKodeFaskes.disabled = false;
    });

});
</script>

