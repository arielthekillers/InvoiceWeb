<?php
include('../includes/header.php');
include('../includes/functions.php');

$getID = $_GET['id'];

// Connect to the database
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

// output any connection error
if ($mysqli->connect_error) {
	die('Error : ('.$mysqli->connect_errno .') '. $mysqli->connect_error);
}

// the query
$query = "SELECT p.*, i.*, c.*
			FROM invoice_items p 
			JOIN invoices i ON i.invoice = p.invoice
			JOIN customers c ON c.invoice = i.invoice
			WHERE p.invoice = '" . $mysqli->real_escape_string($getID) . "'";

$result = mysqli_query($mysqli, $query);

// mysqli select query
if($result) {
	while ($row = mysqli_fetch_assoc($result)) {
		$customer_name = $row['name']; // customer name
		$customer_email = $row['email']; // customer email
		$customer_address_1 = $row['address_1']; // customer address
		$customer_address_2 = $row['address_2']; // customer address
		$customer_town = $row['town']; // customer town
		$customer_county = $row['county']; // customer county
		$customer_postcode = $row['postcode']; // customer postcode
		$customer_phone = $row['phone']; // customer phone number
		
		//shipping
		$customer_name_ship = $row['name_ship']; // customer name (shipping)
		$customer_address_1_ship = $row['address_1_ship']; // customer address (shipping)
		$customer_address_2_ship = $row['address_2_ship']; // customer address (shipping)
		$customer_town_ship = $row['town_ship']; // customer town (shipping)
		$customer_county_ship = $row['county_ship']; // customer county (shipping)
		$customer_postcode_ship = $row['postcode_ship']; // customer postcode (shipping)

		// invoice details
		$invoice_number = $row['invoice']; // invoice number
		$custom_email = $row['custom_email']; // invoice custom email body
		$invoice_date = $row['invoice_date']; // invoice date
		$invoice_due_date = $row['invoice_due_date']; // invoice due date
		$invoice_subtotal = $row['subtotal']; // invoice sub-total
		$invoice_shipping = $row['shipping']; // invoice shipping amount
		$invoice_discount = $row['discount']; // invoice discount
		$invoice_vat = $row['vat']; // invoice vat
		$invoice_total = $row['total']; // invoice total
		$invoice_notes = $row['notes']; // Invoice notes
		$invoice_type = $row['invoice_type']; // Invoice type
		$invoice_status = $row['status']; // Invoice status
	}
}

/* close connection */
$mysqli->close();

?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Edit Invoice (<?php echo $getID; ?>)</h2>
        <hr>
    </div>
</div>

<div id="response" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <div class="message"></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<form method="post" id="update_invoice">
    <input type="hidden" name="action" value="update_invoice">
    <input type="hidden" name="update_id" value="<?php echo $getID; ?>">
    
    <div class="row mb-4">
        <div class="col-md-4">
            <!-- Left empty on purpose -->
        </div>
        <div class="col-md-8 text-end">
            <div class="row g-2 justify-content-end mb-3">
                <div class="col-auto d-flex align-items-center">
                    <h5 class="mb-0 me-2">INVOICE</h5>
                </div>
                <div class="col-auto">
                    <select name="invoice_type" id="invoice_type" class="form-select">
                        <option value="invoice" <?php if($invoice_type === 'invoice'){?>selected<?php } ?>>Invoice</option>
                        <option value="quote" <?php if($invoice_type === 'quote'){?>selected<?php } ?>>Quote</option>
                        <option value="receipt" <?php if($invoice_type === 'receipt'){?>selected<?php } ?>>Receipt</option>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="invoice_status" id="invoice_status" class="form-select">
                        <option value="open" <?php if($invoice_status === 'open'){?>selected<?php } ?>>Open</option>
                        <option value="paid" <?php if($invoice_status === 'paid'){?>selected<?php } ?>>Paid</option>
                    </select>
                </div>
            </div>

            <div class="row g-2 justify-content-end">
                <div class="col-md-4">
                    <div class="input-group" id="invoice_date">
                        <input type="text" class="form-control required" name="invoice_date" placeholder="Invoice Date" data-date-format="<?php echo DATE_FORMAT ?>" value="<?php echo $invoice_date; ?>" />
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group" id="invoice_due_date">
                        <input type="text" class="form-control required" name="invoice_due_date" placeholder="Due Date" data-date-format="<?php echo DATE_FORMAT ?>" value="<?php echo $invoice_due_date; ?>" />
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">#<?php echo INVOICE_PREFIX ?></span>
                        <input type="text" name="invoice_id" id="invoice_id" class="form-control required" placeholder="Invoice Number" value="<?php echo $getID; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_name" id="customer_name" placeholder="Enter name" tabindex="1" value="<?php echo $customer_name; ?>">
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control copy-input required" name="customer_email" id="customer_email" placeholder="E-mail address" tabindex="2" value="<?php echo $customer_email; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_address_1" id="customer_address_1" placeholder="Address 1" tabindex="3" value="<?php echo $customer_address_1; ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input" name="customer_address_2" id="customer_address_2" placeholder="Address 2" tabindex="4" value="<?php echo $customer_address_2; ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_town" id="customer_town" placeholder="Town" tabindex="5" value="<?php echo $customer_town; ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_county" id="customer_county" placeholder="Country" tabindex="6" value="<?php echo $customer_county; ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control copy-input required" name="customer_postcode" id="customer_postcode" placeholder="Postcode" tabindex="7" value="<?php echo $customer_postcode; ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_phone" id="invoice_phone" placeholder="Phone number" tabindex="8" value="<?php echo $customer_phone; ?>">
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
                            <input type="text" class="form-control required" name="customer_name_ship" id="customer_name_ship" placeholder="Enter name" tabindex="9" value="<?php echo $customer_name_ship; ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_address_1_ship" id="customer_address_1_ship" placeholder="Address 1" tabindex="10" value="<?php echo $customer_address_1_ship; ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="customer_address_2_ship" id="customer_address_2_ship" placeholder="Address 2" tabindex="11" value="<?php echo $customer_address_2_ship; ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_town_ship" id="customer_town_ship" placeholder="Town" tabindex="12" value="<?php echo $customer_town_ship; ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_county_ship" id="customer_county_ship" placeholder="Country" tabindex="13" value="<?php echo $customer_county_ship; ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control required" name="customer_postcode_ship" id="customer_postcode_ship" placeholder="Postcode" tabindex="14" value="<?php echo $customer_postcode_ship; ?>">
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
                <?php 
                    $mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
                    if ($mysqli->connect_error) {
                        die('Error : ('.$mysqli->connect_errno .') '. $mysqli->connect_error);
                    }
                    $query2 = "SELECT * FROM invoice_items WHERE invoice = '" . $mysqli->real_escape_string($getID) . "'";
                    $result2 = mysqli_query($mysqli, $query2);

                    if($result2) {
                        while ($rows = mysqli_fetch_assoc($result2)) {
                            $item_product = $rows['product'];
                            $item_qty = $rows['qty'];
                            $item_price = $rows['price'];
                            $item_discount = $rows['discount'];
                            $item_subtotal = $rows['subtotal'];
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-danger btn-sm delete-row me-2"><i class="bi bi-x-lg"></i></a>
                            <input type="text" class="form-control item-input invoice_product" name="invoice_product[]" placeholder="Masukkan deskripsi pekerjaan/jasa" value="<?php echo $item_product; ?>">
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control invoice_product_qty calculate text-end" name="invoice_product_qty[]" value="<?php echo $item_qty; ?>">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text"><?php echo CURRENCY ?></span>
                            <input type="text" class="form-control calculate invoice_product_price required text-end" name="invoice_product_price[]" placeholder="0.00" value="<?php echo $item_price; ?>">
                        </div>
                    </td>
                    <td>
                        <input type="text" class="form-control calculate text-end" name="invoice_product_discount[]" placeholder="% or val" value="<?php echo $item_discount; ?>">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text"><?php echo CURRENCY ?></span>
                            <input type="text" class="form-control calculate-sub text-end" name="invoice_product_sub[]" id="invoice_product_sub" value="<?php echo $item_subtotal; ?>" disabled>
                        </div>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>

    <div class="row" id="invoice_totals">
        <div class="col-md-6 mb-4">
            <textarea class="form-control" name="invoice_notes" rows="4" placeholder="Additional Notes..."><?php echo $invoice_notes; ?></textarea>
            
            <div class="mt-3">
                <textarea name="custom_email" id="custom_email" class="form-control" rows="3" placeholder="Enter custom email if you wish to override the default invoice type email msg!"><?php echo $custom_email; ?></textarea>
            </div>
            
            <div class="mt-4">
                <button type="submit" id="action_edit_invoice" class="btn btn-success" data-loading-text="Updating...">
                    <i class="bi bi-check-circle me-1"></i> Update Invoice
                </button>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-7 text-end fw-bold">Sub Total:</div>
                        <div class="col-5 text-end">
                            <?php echo CURRENCY ?><span class="invoice-sub-total"> <?php echo $invoice_subtotal; ?></span>
                            <input type="hidden" name="invoice_subtotal" id="invoice_subtotal" value="<?php echo $invoice_subtotal; ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-7 text-end fw-bold">Discount:</div>
                        <div class="col-5 text-end">
                            <?php echo CURRENCY ?><span class="invoice-discount"> <?php echo $invoice_discount; ?></span>
                            <input type="hidden" name="invoice_discount" id="invoice_discount" value="<?php echo $invoice_discount; ?>">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-7 text-end fw-bold">Shipping:</div>
                        <div class="col-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><?php echo CURRENCY ?></span>
                                <input type="text" class="form-control calculate shipping text-end" name="invoice_shipping" placeholder="0.00" value="<?php echo $invoice_shipping; ?>">
                            </div>
                        </div>
                    </div>
                    <?php if (ENABLE_VAT == true) { ?>
                    <div class="row mb-2">
                        <div class="col-7 text-end fw-bold">
                            TAX/VAT:
                        </div>
                        <div class="col-5 text-end">
                            <?php echo CURRENCY ?><span class="invoice-vat" data-enable-vat="<?php echo ENABLE_VAT ?>" data-vat-rate="<?php echo VAT_RATE ?>" data-vat-method="<?php echo VAT_INCLUDED ?>"><?php echo $invoice_vat; ?></span>
                            <input type="hidden" name="invoice_vat" id="invoice_vat" value="<?php echo $invoice_vat; ?>">
                        </div>
                    </div>
                    <?php } ?>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-7 text-end fw-bold fs-5">Total:</div>
                        <div class="col-5 text-end fs-5 fw-bold">
                            <?php echo CURRENCY ?><span class="invoice-total"> <?php echo $invoice_total; ?></span>
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
