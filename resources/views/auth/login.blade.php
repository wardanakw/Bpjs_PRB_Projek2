<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - RUBICON+</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-image: url("{{ asset('backgrounds/bg1.jpg') }}");
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Poppins', sans-serif;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.3);
      padding: 40px;
      width: 400px;
      color: black;
    }

    .login-card h3 {
      color: #004aad;
      text-align: center;
      margin-bottom: 25px;
      font-weight: bold;
    }

    .btn-login {
      background-color: #004aad;
      color: white;
      width: 100%;
      border-radius: 10px;
      padding: 10px;
      font-weight: bold;
    }

    .captcha-container {
      background: rgba(255, 255, 255, 0.9);
      border: 2px solid #e9ecef;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }

    .captcha-container:hover {
      border-color: #004aad;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .captcha-container img {
      border-radius: 5px;
      transition: transform 0.2s ease;
    }

    .captcha-container img:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h3>RUBICON+</h3>

@if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif


    <form action="{{ route('login.post') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" placeholder="Masukan Username" required>
      </div>

      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukan Password" required>
      </div>

      <div class="text-center mb-3">
        <div class="captcha-container d-inline-block p-3 border rounded bg-white">
          <img src="{{ captcha_src('flat') }}" id="captcha-img" alt="captcha" class="img-fluid" style="height: 50px; cursor: pointer;" title="Klik untuk refresh captcha">
          <div class="mt-2">
            <button type="button" class="btn btn-sm btn-outline-primary" id="reloadCaptcha">
              <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <input type="text" name="captcha" class="form-control" placeholder="Masukan Kode Captcha" required>
      </div>

      <button type="submit" class="btn btn-login">LOGIN</button>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function refreshCaptcha() {
        $.get('{{ route('refresh.captcha') }}', function(data) {
            $('#captcha-img').attr('src', data.captcha);
        }).fail(function() {
            alert('Gagal memuat captcha baru. Silakan coba lagi.');
        });
    }

    $('#reloadCaptcha').click(function () {
        refreshCaptcha();
    });

    $('#captcha-img').click(function () {
        refreshCaptcha();
    });
  </script>
</body>
</html>
