<div class="modal fade" id="settingAccountModal" tabindex="-1" aria-labelledby="settingAccountLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-body p-0">
        <div class="row g-0">


          <div class="col-md-4 modal-left p-4 text-center">
          <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
              <i class="bi bi-x-lg"></i>
            </button>

            <div class="profile-placeholder mb-3"><i class="bi bi-person"></i></div>
            <h6 class="fw-semibold mb-3">Account Settings</h6>
            <a href="#" id="btnGeneralInfo" class="active" onclick="showSection('general')">Edit Nama</a>
            <a href="#" id="btnManageAccount" onclick="showSection('manage')">Ubah Password</a>
          </div>
          <div class="col-md-8 p-4 bg-white rounded-end-4">
            <h5 class="fw-semibold mb-3">Setting Account</h5>
           <div id="sectionGeneral">
              <form id="updateProfileForm">
                @csrf
                @method('PUT')
                <div class="mb-3">
                  <label class="form-label">Nama</label>
                  <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Role</label>
                  <input type="text" class="form-control" value="{{ ucfirst(Auth::user()->role) }}" readonly>
                </div>
                @if(Auth::user()->role === 'fktp' && Auth::user()->fktp)
                <div class="mb-3">
                  <label class="form-label">Nama FKTP</label>
                  <input type="text" class="form-control" value="{{ Auth::user()->fktp->nama_fktp }}" readonly>
                </div>
                @endif
                @if(Auth::user()->role === 'rumah_sakit' && Auth::user()->rumahSakit)
                <div class="mb-3">
                  <label class="form-label">Nama Rumah Sakit</label>
                  <input type="text" class="form-control" value="{{ Auth::user()->rumahSakit->nama_rs }}" readonly>
                </div>
                @endif
                @if(Auth::user()->role === 'apotek' && Auth::user()->apotek)
                <div class="mb-3">
                  <label class="form-label">Nama Apotek</label>
                  <input type="text" class="form-control" value="{{ Auth::user()->apotek->nama_apotek }}" readonly>
                </div>
                @endif
                <div class="text-end">
                  <button type="submit" class="btn btn-primary rounded-pill px-4">Update Nama</button>
                </div>
              </form>
            </div>

            <div id="sectionManage" class="d-none">
              <form id="updatePasswordForm">
                @csrf
                @method('PUT')
                <div class="mb-3">
                  <label class="form-label">Password Lama</label>
                  <div class="password-input-group">
                    <input type="password" class="form-control" name="old_password" placeholder="Masukkan password lama" id="oldPassword" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('oldPassword', 'oldPasswordIcon')">
                      <i class="bi bi-eye" id="oldPasswordIcon"></i>
                    </button>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Password Baru</label>
                  <div class="password-input-group">
                    <input type="password" class="form-control" name="password" placeholder="Masukkan password baru" id="newPassword" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('newPassword', 'newPasswordIcon')">
                      <i class="bi bi-eye" id="newPasswordIcon"></i>
                    </button>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Konfirmasi Password Baru</label>
                  <div class="password-input-group">
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Ulangi password baru" id="confirmPassword" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', 'confirmPasswordIcon')">
                      <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                    </button>
                  </div>
                </div>
                <div class="text-end">
                  <button type="submit" class="btn btn-primary rounded-pill px-4">Update Password</button>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<style>
  
  .modal-content {
    border-radius: 20px;
  }

  .modal-left {
    background: linear-gradient(180deg, #0078D7, #1888E3, #41B7EA);
    color: white;
    border-radius: 20px 0 0 20px;
    position: relative;
  }

  .modal-left .profile-placeholder {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: #fff;
    color: #0078D7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    margin: 0 auto 10px auto;
  }

  .modal-left a {
    color: white;
    font-weight: 500;
    text-decoration: none;
    display: block;
    margin-bottom: 8px;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  .modal-left a:hover,
  .modal-left a.active {
    font-weight: 600;
    background-color: rgba(255, 255, 255, 0.2);
  }

  .btn-primary {
    background-color: #0078D7;
    border: none;
  }

  .btn-primary:hover {
    background-color: #006cc2;
  }

  label {
    font-weight: 500;
  }

  .modal-close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    z-index: 10;
  }

  .modal-close-btn:hover {
    opacity: 0.8;
  }

  .password-input-group {
    position: relative;
  }

  .password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    z-index: 5;
  }

  .password-toggle:hover {
    color: #0078D7;
  }

  .form-control[type="password"],
  .form-control[type="text"] {
    padding-right: 40px;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const openSetting = document.getElementById('openSetting');
    if (openSetting) {
      openSetting.addEventListener('click', function(e) {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById('settingAccountModal'));
        modal.show();
      });
    }
    // Handle update profile form
    document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      fetch('{{ route("setting.updateProfile") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
  
          const navbarUsername = document.getElementById('navbar-username');
          if (navbarUsername) {
            const newName = formData.get('name');
       
            const rolePart = navbarUsername.innerHTML.split('—')[1] || '';
            navbarUsername.innerHTML = newName + (rolePart ? ' — ' + rolePart : '');
          }
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message);
      });
    });


    document.getElementById('updatePasswordForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      fetch('{{ route("setting.updatePassword") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          this.reset();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message);
      });
    });  });

  function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const passwordIcon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      passwordIcon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
      passwordInput.type = 'password';
      passwordIcon.classList.replace('bi-eye-slash', 'bi-eye');
    }
  }

  function showSection(section) {
    const general = document.getElementById('sectionGeneral');
    const manage = document.getElementById('sectionManage');
    const btnGeneral = document.getElementById('btnGeneralInfo');
    const btnManage = document.getElementById('btnManageAccount');

    if (section === 'general') {
      general.classList.remove('d-none');
      manage.classList.add('d-none');
      btnGeneral.classList.add('active');
      btnManage.classList.remove('active');
    } else {
      general.classList.add('d-none');
      manage.classList.remove('d-none');
      btnGeneral.classList.remove('active');
      btnManage.classList.add('active');
    }
  }

  function nextToPassword() {
    showSection('manage');
  }

  function backToProfile() {
    showSection('general');
  }
</script>
