<?php


include_once(__DIR__ . "/config.php");

// get invoice list
function getInvoices() {

	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : ('.$mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	// the query
    $query = "SELECT * 
		FROM invoices i
		JOIN customers c
		ON c.invoice = i.invoice
		WHERE i.invoice = c.invoice
		ORDER BY i.invoice";

	// mysqli select query
	$results = $mysqli->query($query);

	// mysqli select query
	if($results) {

		print '<table class="table table-sm table-striped table-hover table-bordered mb-0" cellspacing="0"><thead><tr>

				<th>Invoice</th>
				<th>Customer</th>
				<th>Issue Date</th>
				<th>Due Date</th>
				<th>Type</th>
				<th>Status</th>
				<th>Actions</th>

			  </tr></thead><tbody>';

		while($row = $results->fetch_assoc()) {

			print '
				<tr>
					<td>'.$row["invoice"].'</td>
					<td>'.$row["name"].'</td>
				    <td>'.$row["invoice_date"].'</td>
				    <td>'.$row["invoice_due_date"].'</td>
				    <td>'.$row["invoice_type"].'</td>
				';

				if($row['status'] == "open"){
					print '<td><span class="badge bg-primary">'.$row['status'].'</span></td>';
				} elseif ($row['status'] == "paid"){
					print '<td><span class="badge bg-success">'.$row['status'].'</span></td>';
				} else {
					print '<td></td>';
				}

			print '
				    <td>
                        <div class="d-flex gap-1">
                            <a href="invoice-edit.php?id='.$row["invoice"].'" class="btn btn-light btn-sm text-primary border-0" title="Edit"><i class="bi bi-pencil"></i></a> 
                            <a href="#" data-invoice-id="'.$row['invoice'].'" data-email="'.$row['email'].'" data-invoice-type="'.$row['invoice_type'].'" data-custom-email="'.$row['custom_email'].'" class="btn btn-light btn-sm text-success border-0 email-invoice" title="Email"><i class="bi bi-envelope"></i></a> 
                            <a href="' . BASE_URL . 'invoices/'.$row["invoice"].'.pdf" class="btn btn-light btn-sm text-info border-0" target="_blank" title="Download"><i class="bi bi-download"></i></a> 
                            <a data-invoice-id="'.$row['invoice'].'" class="btn btn-light btn-sm text-danger border-0 delete-invoice" title="Delete"><i class="bi bi-trash"></i></a>
                        </div>
                    </td>
			    </tr>
			';

		}

		print '</tr></tbody></table>';

	} else {

		echo "<p>There are no invoices to display.</p>";

	}

	// Frees the memory associated with a result
	$results->free();

	// close connection 
	$mysqli->close();

}

// Initial invoice number
function getInvoiceId() {

	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$query = "SELECT invoice FROM invoices ORDER BY invoice DESC LIMIT 1";

	if ($result = $mysqli->query($query)) {

		$row_cnt = $result->num_rows;

	    $row = mysqli_fetch_assoc($result);

	    //var_dump($row);

	    if($row_cnt == 0){
			echo INVOICE_INITIAL_VALUE;
		} else {
			echo intval($row['invoice']) + 1; 
		}

	    // Frees the memory associated with a result
		$result->free();

		// close connection 
		$mysqli->close();
	}
	
}

// populate product dropdown for invoice creation
function popProductsList() {

	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	// the query
	$query = "SELECT * FROM products ORDER BY product_name ASC";

	// mysqli select query
	$results = $mysqli->query($query);

	if($results) {
		echo '<select class="form-control item-select">';
		while($row = $results->fetch_assoc()) {

		    print '<option value="'.$row['product_price'].'">'.$row["product_name"].' - '.$row["product_desc"].'</option>';
		}
		echo '</select>';

	} else {

		echo "<p>There are no products, please add a product.</p>";

	}

	// Frees the memory associated with a result
	$results->free();

	// close connection 
	$mysqli->close();

}

// populate product dropdown for invoice creation
function popCustomersList() {

	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	// the query
	$query = "SELECT * FROM store_customers ORDER BY name ASC";

	// mysqli select query
	$results = $mysqli->query($query);

	if($results) {

		print '<table class="table table-hover align-middle mb-0"><thead class="table-light"><tr>

				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Action</th>

			  </tr></thead><tbody>';

		while($row = $results->fetch_assoc()) {

		    print '
			    <tr>
					<td>'.$row["name"].'</td>
				    <td>'.$row["email"].'</td>
				    <td>'.$row["phone"].'</td>
				    <td><a href="#" class="btn btn-light btn-sm text-primary border-0 customer-select" data-customer-name="'.$row['name'].'" data-customer-email="'.$row['email'].'" data-customer-phone="'.$row['phone'].'" data-customer-address-1="'.$row['address_1'].'" data-customer-address_2="'.$row['address_2'].'" data-customer-town="'.$row['town'].'" data-customer-county="'.$row['county'].'" data-customer-postcode="'.$row['postcode'].'">Select</a></td>
			    </tr>
		    ';
		}

		print '</tr></tbody></table>';

	} else {

		echo "<p>There are no customers to display.</p>";

	}

	// Frees the memory associated with a result
	$results->free();

	// close connection 
	$mysqli->close();

}



// get user list
function getUsers() {

	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	// the query
	$query = "SELECT * FROM users ORDER BY username ASC";

	// mysqli select query
	$results = $mysqli->query($query);

	if($results) {

		print '<table class="table table-hover align-middle mb-0"><thead class="table-light"><tr>

				<th>Name</th>
				<th>Username</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Action</th>

			  </tr></thead><tbody>';

		while($row = $results->fetch_assoc()) {

		    print '
			    <tr>
			    	<td>'.$row['name'].'</td>
					<td>'.$row["username"].'</td>
				    <td>'.$row["email"].'</td>
				    <td>'.$row["phone"].'</td>
				    <td>
                        <div class="d-flex gap-1">
                            <a href="user-edit.php?id='.$row["id"].'" class="btn btn-light btn-sm text-primary border-0"><i class="bi bi-pencil"></i></a> 
                            <a data-user-id="'.$row['id'].'" class="btn btn-light btn-sm text-danger border-0 delete-user"><i class="bi bi-trash"></i></a>
                        </div>
                    </td>
			    </tr>
		    ';
		}

		print '</tr></tbody></table>';

	} else {

		echo "<p>There are no users to display.</p>";

	}

	// Frees the memory associated with a result
	$results->free();

	// close connection 
	$mysqli->close();
}

// get user list
function getCustomers() {

	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	// the query
	$query = "SELECT * FROM store_customers ORDER BY name ASC";

	// mysqli select query
	$results = $mysqli->query($query);

	if($results) {

		print '<table class="table table-sm table-striped table-hover table-bordered mb-0"><thead><tr>

				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Action</th>

			  </tr></thead><tbody>';

		while($row = $results->fetch_assoc()) {

		    print '
			    <tr>
					<td>'.$row["name"].'</td>
				    <td>'.$row["email"].'</td>
				    <td>'.$row["phone"].'</td>
				    <td>
                        <div class="d-flex gap-1">
                            <a href="customer-edit.php?id='.$row["id"].'" class="btn btn-light btn-sm text-primary border-0"><i class="bi bi-pencil"></i></a> 
                            <a data-customer-id="'.$row['id'].'" class="btn btn-light btn-sm text-danger border-0 delete-customer"><i class="bi bi-trash"></i></a>
                        </div>
                    </td>
			    </tr>
		    ';
		}

		print '</tr></tbody></table>';

	} else {

		echo "<p>There are no customers to display.</p>";

	}

	// Frees the memory associated with a result
	$results->free();

	// close connection 
	$mysqli->close();
}

?>

