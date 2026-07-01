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
$query = "SELECT * FROM store_customers WHERE id = '" . $mysqli->real_escape_string($getID) . "'";

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
		

	}
}

/* close connection */
$mysqli->close();

?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Edit Customer</h2>
        <hr>
    </div>
</div>

<div id="response" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <div class="message"></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<form method="post" id="update_customer">
    <input type="hidden" name="action" value="update_customer">
    <input type="hidden" name="id" value="<?php echo $getID; ?>">
    
    <div class="row g-4">
        <!-- Customer Information -->
        <div class="col-md-12">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Editing Customer (<?php echo $getID; ?>)</h5>
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
                            <input type="text" class="form-control copy-input required" name="customer_town" id="customer_town" placeholder="Town/City" tabindex="5" value="<?php echo $customer_town; ?>">
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
    </div>

    <div class="row mt-4">
        <div class="col-12 text-end">
            <button type="submit" id="action_update_customer" class="btn btn-success" data-loading-text="Updating...">
                <i class="bi bi-check-circle me-1"></i> Update Customer
            </button>
        </div>
    </div>
</form>

<?php
include('../includes/footer.php');
?>
