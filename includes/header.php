<?php
	//check login
	include_once(__DIR__."/session.php");
	include_once(__DIR__."/config.php");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoice Management System</title>
  
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Bootstrap Datetime Picker CSS -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.datetimepicker.css">

  <!-- Custom Styles -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  
  <!-- Moment.js -->
  <script src="<?php echo BASE_URL; ?>assets/js/moment.js"></script>
  
  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Bootstrap Datetimepicker JS -->
  <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.datetime.js"></script>
  
  <!-- Custom Scripts -->
  <script src="<?php echo BASE_URL; ?>assets/js/scripts.js"></script>

  <style>
    body {
        background-color: #f8f9fa;
    }
    .navbar-brand {
        font-weight: bold;
    }
    .content-container {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?php echo BASE_URL; ?>pages/dashboard.php">
      <i class="bi bi-receipt-cutoff me-2"></i>Invoice System
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>pages/dashboard.php"><i class="bi bi-house-door me-1"></i> Home</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="invoiceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-file-earmark-text me-1"></i> Invoice
          </a>
          <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="invoiceDropdown">
            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/invoice-list.php"><i class="bi bi-receipt me-1"></i> Invoices</a></li>
            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/customer-list.php"><i class="bi bi-people me-1"></i> Customers</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>pages/user-list.php"><i class="bi bi-person-badge me-1"></i> Users</a>
        </li>
      </ul>
      
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a href="#" class="d-block text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="rounded-circle bg-light text-success d-inline-flex align-items-center justify-content-center fw-bold me-1" style="width: 32px; height: 32px; font-size: 14px;">
              <?php echo strtoupper(substr($_SESSION['login_username'], 0, 2)); ?>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="dropdownUser1">
            <li><p class="dropdown-item mb-0 fw-bold border-bottom pb-2"><?php echo $_SESSION['login_username']; ?></p></li>
            <li><a class="dropdown-item pt-2 text-danger" href="<?php echo BASE_URL; ?>pages/logout.php"><i class="bi bi-box-arrow-right me-1"></i> Sign out</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container content-container">
