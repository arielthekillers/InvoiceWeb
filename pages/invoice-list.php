<?php
include('../includes/header.php');
include('../includes/functions.php');
?>

<div id="response" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <div class="message"></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">Invoices</h5>
                <a href="invoice-create.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Create Invoice
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <?php getInvoices(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="delete_invoice" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Delete Invoice</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-muted">
        <p>Are you sure you want to delete this invoice? This action cannot be undone.</p>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="delete">Delete</button>
      </div>
    </div>
  </div>
</div>

<?php
include('../includes/footer.php');
?>
