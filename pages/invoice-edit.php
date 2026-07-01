<?php
include('../includes/header.php');
include('../includes/functions.php');

$getID = $_GET['id'];

// Connect to the database
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
if ($mysqli->connect_error) {
	die('Error : ('.$mysqli->connect_errno .') '. $mysqli->connect_error);
}

$query = "SELECT p.*, i.*, c.*
			FROM invoice_items p 
			JOIN invoices i ON i.invoice = p.invoice
			JOIN customers c ON c.invoice = i.invoice
			WHERE p.invoice = '" . $mysqli->real_escape_string($getID) . "'";

$result = mysqli_query($mysqli, $query);

$invoice_date = '';
$invoice_total = 0;
$invoice_discount = 0;
$invoice_notes = '';
$invoice_status = 'open';
$current_customer_name = '';

if($result) {
	while ($row = mysqli_fetch_assoc($result)) {
		$current_customer_name = $row['name'];
		$invoice_number   = $row['invoice'];
		$invoice_date     = $row['invoice_date'];
		$invoice_total    = $row['total'];
		$invoice_discount = $row['discount'];
		$invoice_notes    = $row['notes'];
		$invoice_status   = $row['status'];
	}
}

$mysqli->close();
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Edit Invoice (<?php echo htmlspecialchars($getID); ?>)</h2>
        <hr>
    </div>
</div>

<div id="response" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <div class="message"></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<form method="post" id="update_invoice">
    <input type="hidden" name="action" value="update_invoice">
    <input type="hidden" name="update_id" value="<?php echo htmlspecialchars($getID); ?>">
    <input type="hidden" name="invoice_type" value="invoice">
    <input type="hidden" name="invoice_status" value="<?php echo htmlspecialchars($invoice_status); ?>">
    <!-- Hidden field stores DD/MM/YYYY formatted date for PHP -->
    <input type="hidden" name="invoice_date" id="invoice_date_formatted" value="<?php echo htmlspecialchars($invoice_date); ?>">

    <!-- Invoice Header: Date + Number -->
    <div class="row mb-4 g-3 justify-content-end">
        <div class="col-md-4">
            <?php 
                // Determine format based on contents
                if (strpos($invoice_date, '/') !== false) {
                    $dateParts = explode('/', $invoice_date);
                    $isoDate = count($dateParts) == 3 ? $dateParts[2].'-'.$dateParts[1].'-'.$dateParts[0] : '';
                } else {
                    $isoDate = $invoice_date; // already YYYY-MM-DD
                }
            ?>
            <input type="date" class="form-control required" id="invoice_date_raw"
                   placeholder="Pilih Tanggal" value="<?php echo htmlspecialchars($isoDate); ?>" />
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text">#</span>
                <input type="text" name="invoice_id" id="invoice_id" class="form-control required"
                       placeholder="No. Invoice" value="<?php echo htmlspecialchars($getID); ?>">
            </div>
        </div>
    </div>

    <!-- Customer Selection -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>Pelanggan
                        <?php if($current_customer_name): ?>
                            <small class="text-muted fw-normal ms-2">— Saat ini: <strong><?php echo htmlspecialchars($current_customer_name); ?></strong></small>
                        <?php endif; ?>
                    </h5>
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
                <?php 
                    $mysqli2 = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
                    if ($mysqli2->connect_error) {
                        die('Error : ('.$mysqli2->connect_errno .') '. $mysqli2->connect_error);
                    }
                    $query2 = "SELECT * FROM invoice_items WHERE invoice = '" . $mysqli2->real_escape_string($getID) . "'";
                    $result2 = mysqli_query($mysqli2, $query2);

                    if($result2) {
                        while ($rows = mysqli_fetch_assoc($result2)) {
                            $item_product = $rows['product'];
                            $item_price   = floatval($rows['price']);
                            $item_foc     = ($rows['discount'] == $item_price && $item_price >= 0 && intval($rows['subtotal']) == 0);
                            // A simpler FOC check: if subtotal is 0 and original price was stored in discount
                            // Actually store FOC as: price in DB = original price, subtotal = 0 if FOC
                            // We'll check: if subtotal == 0 then it's FOC
                            $item_foc     = (floatval($rows['subtotal']) == 0 && $item_price > 0) || ($rows['discount'] > 0 && floatval($rows['subtotal']) == 0);
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-danger btn-sm delete-row me-2"><i class="bi bi-x-lg"></i></a>
                            <textarea class="form-control invoice_product" name="invoice_product[]"
                                      placeholder="Masukkan deskripsi pekerjaan/jasa"
                                      rows="1" style="resize: vertical; min-height: 38px; height: auto;"><?php echo htmlspecialchars($item_product); ?></textarea>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calculate invoice_product_price required text-end"
                                   name="invoice_product_price[]" placeholder="0" min="0"
                                   value="<?php echo $item_price; ?>"
                                   <?php echo $item_foc ? 'data-original-price="'.$item_price.'" disabled' : ''; ?>>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="form-check d-flex justify-content-center align-items-center gap-1">
                            <input type="checkbox" class="form-check-input foc-checkbox"
                                   name="invoice_product_foc[]" value="1"
                                   title="Gratis / Free of Charge"
                                   <?php echo $item_foc ? 'checked' : ''; ?>>
                            <label class="form-check-label small text-muted">Gratis</label>
                        </div>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>

    <!-- Totals + Notes -->
    <div class="row" id="invoice_totals">
        <div class="col-md-6 mb-4">
            <textarea class="form-control mb-3" name="invoice_notes" rows="4" placeholder="Catatan tambahan..."><?php echo htmlspecialchars($invoice_notes); ?></textarea>
            <button type="submit" id="action_edit_invoice" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i> Update Invoice
            </button>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-7 text-end fw-bold fs-5">Total:</div>
                        <div class="col-5 text-end fs-5 fw-bold">
                            Rp <span class="invoice-total"><?php echo number_format($invoice_total, 0, '.', '.'); ?></span>
                            <input type="hidden" name="invoice_total" id="invoice_total" value="<?php echo $invoice_total; ?>">
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
