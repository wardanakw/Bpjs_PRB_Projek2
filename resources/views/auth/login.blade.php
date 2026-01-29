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
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Poppins', sans-serif;
      padding: 20px;
    }

    .login-container {
      display: flex;
      gap: 30px;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      max-width: 900px;
    }

    .login-image {
      flex: 1;
      min-width: 280px;
      max-width: 320px;
    }

    .login-image img {
      width: 100%;
      height: auto;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 82, 163, 0.4), 0 4px 16px rgba(0,0,0,0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-image img:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px rgba(0, 82, 163, 0.5), 0 6px 20px rgba(0,0,0,0.25);
    }

    .login-card {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.3);
      padding: 40px;
      width: 400px;
      color: black;
      flex-shrink: 0;
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

    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
        gap: 20px;
      }

      .login-card {
        width: 100%;
        max-width: 400px;
      }

      .login-image {
        max-width: 280px;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-image">
      <img src="{{ asset('backgrounds/prinsip-prb-3b.svg') }}" alt="PRINSIP PRB - 3B">
    </div>
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
