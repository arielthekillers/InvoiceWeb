<?php
/*******************************************************************************
*  Invoice Management System                                                *
*                                                                              *
* Version: 1.0	                                                               *
* Developer:  Abhishek Raj                                   				           *
*******************************************************************************/

include('../includes/header.php');
include('../includes/functions.php');

?>

<div class="row mb-4">
  <div class="col-12">
    <h2 class="fw-bold">Dashboard</h2>
    <hr>
  </div>
</div>

<div class="row g-4">
  <!-- Total Sales -->
  <div class="col-lg-3 col-md-6">
    <div class="card text-white bg-success h-100 shadow-sm border-0">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <h6 class="card-title text-uppercase fw-bold opacity-75">Sales Amount</h6>
          <h2 class="card-text mb-0">
            <?php 
              $result = mysqli_query($mysqli, 'SELECT SUM(subtotal) AS value_sum FROM invoices WHERE status = "paid"'); 
              $row = mysqli_fetch_assoc($result); 
              $sum = $row['value_sum'];
              echo $sum ? $sum : '0';
            ?>
          </h2>
        </div>
        <div class="mt-3 text-end opacity-50">
          <i class="bi bi-currency-dollar fs-1"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Invoices -->
  <div class="col-lg-3 col-md-6">
    <div class="card text-white bg-primary h-100 shadow-sm border-0">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <h6 class="card-title text-uppercase fw-bold opacity-75">Total Invoices</h6>
          <h2 class="card-text mb-0">
            <?php 
              $sql = "SELECT * FROM invoices";
              $query = $mysqli->query($sql);
              echo "$query->num_rows";
            ?>
          </h2>
        </div>
        <div class="mt-3 text-end opacity-50">
          <i class="bi bi-receipt fs-1"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Pending Bills Count -->
  <div class="col-lg-3 col-md-6">
    <div class="card text-white bg-warning h-100 shadow-sm border-0">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <h6 class="card-title text-uppercase fw-bold opacity-75">Pending Bills</h6>
          <h2 class="card-text mb-0">
            <?php 
              $sql = "SELECT * FROM invoices WHERE status = 'open'";
              $query = $mysqli->query($sql);
              echo "$query->num_rows";
            ?>
          </h2>
        </div>
        <div class="mt-3 text-end opacity-50">
          <i class="bi bi-hourglass-split fs-1"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Due Amount -->
  <div class="col-lg-3 col-md-6">
    <div class="card text-white bg-danger h-100 shadow-sm border-0">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <h6 class="card-title text-uppercase fw-bold opacity-75">Due Amount</h6>
          <h2 class="card-text mb-0">
            <?php 
              $result = mysqli_query($mysqli, 'SELECT SUM(subtotal) AS value_sum FROM invoices WHERE status = "open"'); 
              $row = mysqli_fetch_assoc($result); 
              $sum = $row['value_sum'];
              echo $sum ? $sum : '0';
            ?>
          </h2>
        </div>
        <div class="mt-3 text-end opacity-50">
          <i class="bi bi-exclamation-circle fs-1"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mt-1">
  <!-- Total Customers -->
  <div class="col-lg-3 col-md-6">
    <div class="card text-white bg-info h-100 shadow-sm border-0">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <h6 class="card-title text-uppercase fw-bold opacity-75">Total Customers</h6>
          <h2 class="card-text mb-0">
            <?php 
              $sql = "SELECT * FROM store_customers";
              $query = $mysqli->query($sql);
              echo "$query->num_rows";
            ?>
          </h2>
        </div>
        <div class="mt-3 text-end opacity-50">
          <i class="bi bi-people fs-1"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Paid Bills Count -->
  <div class="col-lg-3 col-md-6">
    <div class="card text-white bg-secondary h-100 shadow-sm border-0">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <h6 class="card-title text-uppercase fw-bold opacity-75">Paid Bills</h6>
          <h2 class="card-text mb-0">
            <?php 
              $sql = "SELECT * FROM invoices WHERE status = 'paid'";
              $query = $mysqli->query($sql);
              echo "$query->num_rows";
            ?>
          </h2>
        </div>
        <div class="mt-3 text-end opacity-50">
          <i class="bi bi-check-circle fs-1"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
	include('../includes/footer.php');
?>
