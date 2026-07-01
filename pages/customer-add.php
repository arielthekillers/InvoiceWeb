<?php
include('../includes/header.php');
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Add Customer</h2>
        <hr>
    </div>
</div>

<div id="response" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <div class="message"></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<form method="post" id="create_customer">
    <input type="hidden" name="action" value="create_customer">
    
    <div class="row g-4">
        <!-- Customer Information -->
        <div class="col-md-12">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_name" id="customer_name" placeholder="Enter Name" tabindex="1">
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control copy-input required" name="customer_email" id="customer_email" placeholder="Email Address" tabindex="2">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_address_1" id="customer_address_1" placeholder="Address 1" tabindex="3">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input" name="customer_address_2" id="customer_address_2" placeholder="Address 2" tabindex="4">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_town" id="customer_town" placeholder="Town/City" tabindex="5">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_county" id="customer_county" placeholder="Country" tabindex="6">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_postcode" id="customer_postcode" placeholder="Postcode" tabindex="7">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_phone" id="invoice_phone" placeholder="Phone Number" tabindex="8">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 text-end">
            <button type="submit" id="action_create_customer" class="btn btn-success" data-loading-text="Creating...">
                <i class="bi bi-person-plus-fill me-1"></i> Create Customer
            </button>
        </div>
    </div>
</form>

<?php
include('../includes/footer.php');
?>
