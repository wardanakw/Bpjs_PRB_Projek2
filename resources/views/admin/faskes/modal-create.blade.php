<div class="modal fade" id="createFaskesModal" tabindex="-1" aria-labelledby="createFaskesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title" id="createFaskesModalLabel">Tambah Fasilitas Kesehatan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.faskes.store') }}" method="POST" id="createFaskesForm">
        @csrf
        
    
        <input type="hidden" id="actual_kode_faskes" name="kode_faskes" value="{{ old('kode_faskes') }}">
        <input type="hidden" id="actual_kode_apotek" name="kode_apotek" value="{{ old('kode_apotek') }}">
        
        <div class="modal-body">
          <div class="row g-3">
            
           
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nama User <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                     value="{{ old('name') }}" required placeholder="Nama lengkap user">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

        
            <div class="col-md-6">
              <label class="form-label fw-semibold">Role Faskes <span class="text-danger">*</span></label>
              <select name="role" id="role_faskes" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">-- Pilih Role --</option>
                <option value="fktp" {{ old('role') == 'fktp' ? 'selected' : '' }}>FKTP</option>
                <option value="rumah_sakit" {{ old('role') == 'rumah_sakit' ? 'selected' : '' }}>Rumah Sakit</option>
                <option value="apotek" {{ old('role') == 'apotek' ? 'selected' : '' }}>Apotek</option>
              </select>
              @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

           
            <div class="col-md-6" id="kode-faskes-container">
              <label class="form-label fw-semibold">Kode Faskes <span class="text-danger">*</span></label>
              <input type="text" id="display_kode_faskes" class="form-control" 
                     placeholder="Contoh: FKTP001, RS001" 
                     value="{{ old('role') != 'apotek' ? old('kode_faskes') : '' }}">
              <small class="text-muted">Untuk FKTP dan Rumah Sakit</small>
            </div>

        
            <div class="col-md-6" id="kode-apotek-container" style="display: none;">
              <label class="form-label fw-semibold">Kode Apotek <span class="text-danger">*</span></label>
              <select id="display_kode_apotek" class="form-select">
                <option value="">-- Pilih Kode Apotek --</option>
                @foreach($relasi as $row)
                  <option value="{{ $row->kode_apotek }}"
                    {{ old('kode_apotek') == $row->kode_apotek ? 'selected' : '' }}>
                    {{ $row->kode_apotek }} - {{ $row->nama_apotek }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">Kode apotek dari relasi FKTP</small>
            </div>

           
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nama Faskes <span class="text-danger">*</span></label>
              <input type="text" name="nama_faskes" class="form-control @error('nama_faskes') is-invalid @enderror" 
                     value="{{ old('nama_faskes') }}" required>
              @error('nama_faskes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          
            <div class="col-md-6">
              <label class="form-label fw-semibold">Jenis Faskes</label>
              <input type="text" name="jenis_faskes" class="form-control @error('jenis_faskes') is-invalid @enderror" 
                     value="{{ old('jenis_faskes') }}">
              @error('jenis_faskes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            
            <div class="col-md-12">
              <label class="form-label fw-semibold">Alamat Faskes</label>
              <input type="text" name="alamat_faskes" class="form-control @error('alamat_faskes') is-invalid @enderror" 
                     value="{{ old('alamat_faskes') }}">
              @error('alamat_faskes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

      
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kecamatan</label>
              <input type="text" name="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror" 
                     value="{{ old('kecamatan') }}">
              @error('kecamatan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

           
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kabupaten/Kota</label>
              <input type="text" name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror" 
                     value="{{ old('kabupaten') }}">
              @error('kabupaten')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          
            <div class="col-md-6">
              <label class="form-label fw-semibold">Provinsi</label>
              <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror" 
                     value="{{ old('provinsi') }}">
              @error('provinsi')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kode Pos</label>
              <input type="text" name="kode_pos" class="form-control @error('kode_pos') is-invalid @enderror" 
                     value="{{ old('kode_pos') }}" pattern="[0-9]*">
              @error('kode_pos')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

        
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nomor PIC</label>
              <input type="tel" name="nomor_pic" class="form-control @error('nomor_pic') is-invalid @enderror" 
                     value="{{ old('nomor_pic') }}" placeholder="Contoh: 081234567890">
              @error('nomor_pic')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <hr class="mt-4 mb-2">

          
            <div class="col-md-6">
              <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                     value="{{ old('username') }}" required>
              @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                     required>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" id="submitFormBtn" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== FASKES FORM INITIALIZATION ===');
    
    // Gunakan selector yang spesifik untuk modal ini
    const modal = document.getElementById('createFaskesModal');
    if (!modal) {
        console.error('Modal not found!');
        return;
    }
    
    // Cari elemen DI DALAM modal saja
    const roleSelect = modal.querySelector('#role_faskes');
    const kodeFaskesContainer = modal.querySelector('#kode-faskes-container');
    const kodeApotekContainer = modal.querySelector('#kode-apotek-container');
    const displayKodeFaskes = modal.querySelector('#display_kode_faskes');
    const displayKodeApotek = modal.querySelector('#display_kode_apotek');
    const actualKodeFaskes = modal.querySelector('#actual_kode_faskes');
    const actualKodeApotek = modal.querySelector('#actual_kode_apotek');
    const submitBtn = modal.querySelector('#submitFormBtn');
    const form = modal.querySelector('#createFaskesForm');
    
    
    if (!roleSelect || !form) {
        console.error('Essential elements not found in modal!');
        return;
    }
    
    // Fungsi untuk toggle field
    function updateFields() {
        const role = roleSelect.value;
        console.log('Role selected:', role);
        
        if (role === 'apotek') {
            // Tampilkan apotek, sembunyikan faskes
            if (kodeFaskesContainer) kodeFaskesContainer.style.display = 'none';
            if (kodeApotekContainer) kodeApotekContainer.style.display = 'block';
            console.log('Showing apotek field');
        } else if (role) {
            // Tampilkan faskes, sembunyikan apotek (untuk fktp dan rumah_sakit)
            if (kodeFaskesContainer) kodeFaskesContainer.style.display = 'block';
            if (kodeApotekContainer) kodeApotekContainer.style.display = 'none';
            console.log('Showing faskes field for role:', role);
        }
    }
    
    
    roleSelect.addEventListener('change', updateFields);
    
   
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Submit button clicked');
            
            const role = roleSelect.value;
            console.log('Current role:', role);
            
            if (!role) {
                alert('Silakan pilih Role Faskes terlebih dahulu');
                roleSelect.focus();
                return;
            }
            
            let isValid = true;
            
            // Set nilai ke hidden fields berdasarkan role
            if (role === 'apotek') {
                const selectedApotek = displayKodeApotek ? displayKodeApotek.value : '';
                console.log('Selected apotek value:', selectedApotek);
                
                if (!selectedApotek) {
                    alert('Silakan pilih Kode Apotek');
                    if (displayKodeApotek) displayKodeApotek.focus();
                    isValid = false;
                } else {
                    // Untuk apotek, gunakan kode_apotek sebagai kode_faskes juga
                    if (actualKodeApotek) actualKodeApotek.value = selectedApotek;
                    if (actualKodeFaskes) actualKodeFaskes.value = selectedApotek;
                    console.log('Set values for apotek:', {
                        kode_apotek: selectedApotek,
                        kode_faskes: selectedApotek
                    });
                }
            } else {
                const kodeFaskesValue = displayKodeFaskes ? displayKodeFaskes.value.trim() : '';
                console.log('Faskes code value:', kodeFaskesValue);
                
                if (!kodeFaskesValue) {
                    alert('Silakan isi Kode Faskes');
                    if (displayKodeFaskes) displayKodeFaskes.focus();
                    isValid = false;
                } else {
                    if (actualKodeFaskes) actualKodeFaskes.value = kodeFaskesValue;
                    if (actualKodeApotek) actualKodeApotek.value = '';
                    console.log('Set values for non-apotek:', {
                        kode_faskes: kodeFaskesValue,
                        kode_apotek: ''
                    });
                }
            }
            
            if (isValid) {
                console.log('Validation passed, submitting form...');
                console.log('Form data to submit:', {
                    role: role,
                    kode_faskes: actualKodeFaskes ? actualKodeFaskes.value : '',
                    kode_apotek: actualKodeApotek ? actualKodeApotek.value : ''
                });
                
                form.submit();
            }
        });
    }
    
    updateFields();
    
    // Handle modal events
    modal.addEventListener('show.bs.modal', function() {
        console.log('Modal opened');
        updateFields();
    });
    
    modal.addEventListener('hidden.bs.modal', function() {
        console.log('Modal closed');
  
        if (form) form.reset();
        if (roleSelect) roleSelect.value = '';
        if (displayKodeFaskes) displayKodeFaskes.value = '';
        if (displayKodeApotek) displayKodeApotek.value = '';
        if (actualKodeFaskes) actualKodeFaskes.value = '';
        if (actualKodeApotek) actualKodeApotek.value = '';
        updateFields();
    });
    
    console.log('=== FASKES FORM INITIALIZED SUCCESSFULLY ===');
});

function debugFaskesForm() {
    console.log('=== FASKES FORM DEBUG ===');
    const modal = document.getElementById('createFaskesModal');
    if (modal) {
        console.log('Modal exists');
        const roleSelect = modal.querySelector('#role_faskes');
        console.log('Role select value:', roleSelect ? roleSelect.value : 'NOT FOUND');
    } else {
        console.error('Modal not found!');
    }
}
</script>