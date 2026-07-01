<?php

include('../includes/header.php');
include('../includes/functions.php');

?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Buat Invoice Baru</h2>
        <hr>
    </div>
</div>

<div id="response" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <div class="message"></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<form method="post" id="create_invoice">
    <input type="hidden" name="action" value="create_invoice">
    <input type="hidden" name="invoice_type" value="invoice">
    <!-- Hidden field stores DD/MM/YYYY formatted date for PHP -->
    <input type="hidden" name="invoice_date" id="invoice_date_formatted">
    
    <!-- Invoice Header: Date + Number -->
    <div class="row mb-4 g-3 justify-content-end">
        <div class="col-md-4">
            <input type="date" class="form-control required" id="invoice_date_raw"
                   placeholder="Pilih Tanggal" />
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text">#</span>
                <input type="text" name="invoice_id" id="invoice_id" class="form-control required" 
                       placeholder="Otomatis dari tanggal" readonly
                       style="background:#f8f9fa; font-weight:600;">
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text">Status</span>
                <select name="invoice_status" id="invoice_status" class="form-select">
                    <option value="open" selected>Open</option>
                    <option value="paid">Paid</option>
                    <option value="canceled">Canceled</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Customer Selection -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Pelanggan</h5>
                </div>
                <div class="card-body">
                    <select name="customer" id="customer_select" class="form-select required">
                        <option value="" disabled selected>-- Pilih Pelanggan --</option>
                        <?php popCustomersSelect(); ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-hover align-middle" id="invoice_table">
            <thead class="table-dark">
                <tr>
                    <th style="width:55%">
                        <a href="#" class="btn btn-success btn-sm add-row me-2"><i class="bi bi-plus-lg"></i></a>
                        Deskripsi Pekerjaan / Jasa
                    </th>
                    <th style="width:30%" class="text-end">Biaya (Rp)</th>
                    <th style="width:15%" class="text-center">FOC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="d-flex align-items-center mb-2">
                            <a href="#" class="btn btn-danger btn-sm delete-row me-2"><i class="bi bi-x-lg"></i></a>
                            <textarea class="form-control invoice_product" name="invoice_product[]"
                                      placeholder="Masukkan deskripsi pekerjaan/jasa"
                                      rows="1" style="resize: vertical; min-height: 38px; height: auto;"></textarea>
                        </div>
                        <div class="ps-5 sub-items-wrapper">
                            <input type="hidden" class="invoice_product_desc_hidden" name="invoice_product_desc[]" value="">
                            <div class="sub-items-list">
                                <!-- Dynamic sub items will be appended here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary add-sub-item mt-1">
                                <i class="bi bi-plus"></i> Tambah Sub Item
                            </button>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calculate invoice_product_price required text-end" name="invoice_product_price[]" placeholder="0" min="0">
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="form-check d-flex justify-content-center align-items-center gap-1">
                            <input type="hidden" name="invoice_product_foc[]" value="0" class="foc-hidden">
                            <input type="checkbox" class="form-check-input foc-checkbox" title="Gratis / Free of Charge">
                            <label class="form-check-label small text-muted">Gratis</label>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Totals + Notes -->
    <div class="row" id="invoice_totals">
        <div class="col-md-6 mb-4">
            <textarea class="form-control mb-3" name="invoice_notes" rows="4" placeholder="Catatan tambahan..."></textarea>
            <button type="submit" id="action_create_invoice" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i> Buat Invoice
            </button>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-7 text-end fw-bold fs-5">Total:</div>
                        <div class="col-5 text-end fs-5 fw-bold">
                            Rp <span class="invoice-total">0</span>
                            <input type="hidden" name="invoice_total" id="invoice_total" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
include('../includes/footer.php');
?>
