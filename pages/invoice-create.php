<?php

include('../includes/header.php');
include('../includes/functions.php');

?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Create New <span class="invoice_type">Invoice</span></h2>
        <hr>
    </div>
</div>

<div id="response" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <div class="message"></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<form method="post" id="create_invoice">
    <input type="hidden" name="action" value="create_invoice">
    
    <div class="row mb-4">
        <div class="col-md-4">
            <!-- Left empty on purpose to match original layout -->
        </div>
        <div class="col-md-8 text-end">
            <div class="row g-2 justify-content-end mb-3">
                <div class="col-auto d-flex align-items-center">
                    <h5 class="mb-0 me-2">Select Type:</h5>
                </div>
                <div class="col-auto">
                    <select name="invoice_type" id="invoice_type" class="form-select">
                        <option value="invoice" selected>Invoice</option>
                        <option value="quote">Quote</option>
                        <option value="receipt">Receipt</option>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="invoice_status" id="invoice_status" class="form-select">
                        <option value="open" selected>Open</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>

            <div class="row g-2 justify-content-end">
                <div class="col-md-4">
                    <div class="input-group" id="invoice_date">
                        <input type="text" class="form-control required" name="invoice_date" placeholder="Invoice Date" data-date-format="<?php echo DATE_FORMAT ?>" />
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group" id="invoice_due_date">
                        <input type="text" class="form-control required" name="invoice_due_date" placeholder="Due Date" data-date-format="<?php echo DATE_FORMAT ?>" />
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">#<?php echo INVOICE_PREFIX ?></span>
                        <input type="text" name="invoice_id" id="invoice_id" class="form-control required" placeholder="Invoice Number" value="<?php getInvoiceId(); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Customer Information</h5>
                    <a href="#" class="select-customer text-decoration-none"><b>OR</b> Select Existing</a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_name" id="customer_name" placeholder="Enter Name" tabindex="1">
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control copy-input required" name="customer_email" id="customer_email" placeholder="E-mail Address" tabindex="2">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_address_1" id="customer_address_1" placeholder="Address 1" tabindex="3">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input" name="customer_address_2" id="customer_address_2" placeholder="Address 2" tabindex="4">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_town" id="customer_town" placeholder="Town" tabindex="5">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_county" id="customer_county" placeholder="Country" tabindex="6">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_postcode" id="customer_postcode" placeholder="Postcode" tabindex="7">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_phone" id="customer_phone" placeholder="Phone Number" tabindex="8">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white text-end">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_name_ship" id="customer_name_ship" placeholder="Enter Name" tabindex="9">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_address_1_ship" id="customer_address_1_ship" placeholder="Address 1" tabindex="10">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="customer_address_2_ship" id="customer_address_2_ship" placeholder="Address 2" tabindex="11">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_town_ship" id="customer_town_ship" placeholder="Town" tabindex="12">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_county_ship" id="customer_county_ship" placeholder="Country" tabindex="13">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_postcode_ship" id="customer_postcode_ship" placeholder="Postcode" tabindex="14">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-hover table-striped align-middle" id="invoice_table">
            <thead class="table-light">
                <tr>
                    <th width="40%">
                        <a href="#" class="btn btn-success btn-sm add-row me-2"><i class="bi bi-plus-lg"></i></a>
                        Deskripsi Pekerjaan / Jasa
                    </th>
                    <th width="10%">Qty</th>
                    <th width="20%">Price</th>
                    <th width="15%">Discount</th>
                    <th width="15%">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-danger btn-sm delete-row me-2"><i class="bi bi-x-lg"></i></a>
                            <input type="text" class="form-control item-input invoice_product" name="invoice_product[]" placeholder="Masukkan deskripsi pekerjaan/jasa">
                        </div>
                    </td>
                    <td>
                        <input type="number" class="form-control invoice_product_qty calculate text-end" name="invoice_product_qty[]" value="1">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text"><?php echo CURRENCY ?></span>
                            <input type="number" class="form-control calculate invoice_product_price required text-end" name="invoice_product_price[]" placeholder="0.00">
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control calculate text-end" name="invoice_product_discount[]" placeholder="% or val">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text"><?php echo CURRENCY ?></span>
                            <input type="text" class="form-control calculate-sub text-end" name="invoice_product_sub[]" id="invoice_product_sub" value="0.00" disabled>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="row" id="invoice_totals">
        <div class="col-md-6 mb-4">
            <textarea class="form-control" name="invoice_notes" rows="4" placeholder="Additional Notes..."></textarea>
            
            <div class="mt-3">
                <textarea name="custom_email" id="custom_email" class="form-control" rows="3" placeholder="Enter custom email if you wish to override the default invoice type email msg!"></textarea>
            </div>
            
            <div class="mt-4">
                <button type="submit" id="action_create_invoice" class="btn btn-success" data-loading-text="Creating...">
                    <i class="bi bi-check-circle me-1"></i> Create Invoice
                </button>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-7 text-end fw-bold">Sub Total:</div>
                        <div class="col-5 text-end">
                            <?php echo CURRENCY ?><span class="invoice-sub-total">0.00</span>
                            <input type="hidden" name="invoice_subtotal" id="invoice_subtotal">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-7 text-end fw-bold">Discount:</div>
                        <div class="col-5 text-end">
                            <?php echo CURRENCY ?><span class="invoice-discount">0.00</span>
                            <input type="hidden" name="invoice_discount" id="invoice_discount">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-7 text-end fw-bold">Shipping:</div>
                        <div class="col-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><?php echo CURRENCY ?></span>
                                <input type="text" class="form-control calculate shipping text-end" name="invoice_shipping" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <?php if (ENABLE_VAT == true) { ?>
                    <div class="row mb-2">
                        <div class="col-7 text-end fw-bold">
                            TAX/VAT:<br>
                            <small class="text-muted fw-normal">Remove <input type="checkbox" class="remove_vat"></small>
                        </div>
                        <div class="col-5 text-end">
                            <?php echo CURRENCY ?><span class="invoice-vat" data-enable-vat="<?php echo ENABLE_VAT ?>" data-vat-rate="<?php echo VAT_RATE ?>" data-vat-method="<?php echo VAT_INCLUDED ?>">0.00</span>
                            <input type="hidden" name="invoice_vat" id="invoice_vat">
                        </div>
                    </div>
                    <?php } ?>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-7 text-end fw-bold fs-5">Total:</div>
                        <div class="col-5 text-end fs-5 fw-bold">
                            <?php echo CURRENCY ?><span class="invoice-total">0.00</span>
                            <input type="hidden" name="invoice_total" id="invoice_total">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div id="insert_customer" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select An Existing Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
            <?php popCustomersList(); ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<?php
include('../includes/footer.php');
?>
