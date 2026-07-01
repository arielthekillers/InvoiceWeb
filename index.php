<?php
// index.php — Login page (pure PHP, no JS)
session_start();
include_once('includes/config.php');

$error = '';

// If already logged in, redirect to dashboard
if (isset($_SESSION['login_username'])) {
    header("Location: pages/dashboard.php");
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan Password tidak boleh kosong.';
    } else {
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['login_username'] = $row['username'];
                header("Location: pages/dashboard.php");
                exit;
            } else {
                $error = 'Password salah. Silakan coba lagi.';
            }
        } else {
            $error = 'Username tidak ditemukan.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — Invoice System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      background: linear-gradient(135deg, #1a7a4a 0%, #0f4d2e 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      background: white;
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .login-logo {
      max-height: 70px;
      object-fit: contain;
    }
    .form-control {
      border-radius: 8px;
      padding: 0.65rem 1rem;
      font-size: 0.95rem;
      border-color: #dee2e6;
    }
    .form-control:focus {
      border-color: #1a7a4a;
      box-shadow: 0 0 0 0.2rem rgba(26,122,74,.15);
    }
    .btn-login {
      background: linear-gradient(135deg, #1a7a4a, #0f4d2e);
      border: none;
      border-radius: 8px;
      padding: 0.7rem;
      font-weight: 600;
      letter-spacing: 0.3px;
      transition: opacity 0.2s;
    }
    .btn-login:hover { opacity: 0.9; }
    .input-icon-wrapper { position: relative; }
    .input-icon-wrapper .bi {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #6c757d;
    }
    .input-icon-wrapper .form-control {
      padding-left: 2.2rem;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="text-center mb-4">
      <img src="<?php echo BASE_URL; ?>images/logo.png" class="login-logo mb-3" alt="Logo" onerror="this.style.display='none'">
      <h5 class="fw-bold mb-0">Invoice System</h5>
      <p class="text-muted small mt-1"><?php echo COMPANY_NAME; ?></p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i><?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label fw-semibold small">Username</label>
        <div class="input-icon-wrapper">
          <i class="bi bi-person"></i>
          <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" placeholder="Masukkan username" required autofocus>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold small">Password</label>
        <div class="input-icon-wrapper">
          <i class="bi bi-lock"></i>
          <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
        </div>
      </div>
      <button type="submit" class="btn btn-login btn-primary text-white w-100">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
      </button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>