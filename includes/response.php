<?php


include_once(__DIR__ . '/config.php');

// hide PHP errors in JSON response
ini_set('display_errors', 0);

// output any connection error
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

$action = isset($_POST['action']) ? $_POST['action'] : "";

// ===== Get next invoice number based on selected date =====
if ($action == 'get_invoice_number') {
	header('Content-Type: application/json');
	$dateStr = $_POST['date'] ?? ''; // expected DD/MM/YYYY
	$parts = explode('/', trim($dateStr));
	if (count($parts) == 3) {
		$day   = str_pad(intval($parts[0]), 2, '0', STR_PAD_LEFT);
		$month = str_pad(intval($parts[1]), 2, '0', STR_PAD_LEFT);
		$year  = substr($parts[2], -2); // last 2 digits e.g. "26" from "2026"
		$prefix = INVOICE_PREFIX . $day . $month . $year; // e.g. INV210426
		$like   = $prefix . '%';
		$stmt = $mysqli->prepare("SELECT COUNT(*) AS cnt FROM invoices WHERE invoice LIKE ?");
		$stmt->bind_param('s', $like);
		$stmt->execute();
		$res = $stmt->get_result()->fetch_assoc();
		$seq = intval($res['cnt']) + 1;
		$invoice_number = $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
		echo json_encode(['status' => 'Success', 'invoice_number' => $invoice_number]);
	} else {
		echo json_encode(['status' => 'Error', 'message' => 'Format tanggal tidak valid']);
	}
	exit;
}

if ($action == 'email_invoice'){

	$fileId = $_POST['id'];
	$emailId = $_POST['email'];
	$invoice_type = $_POST['invoice_type'];
	$custom_email = $_POST['custom_email'];

	require_once('class.phpmailer.php');

	$mail = new PHPMailer(); // defaults to using php "mail()"

	$mail->AddReplyTo(EMAIL_FROM, EMAIL_NAME);
	$mail->SetFrom(EMAIL_FROM, EMAIL_NAME);
	$mail->AddAddress($emailId, "");

	$mail->Subject = EMAIL_SUBJECT;
	//$mail->AltBody = EMAIL_BODY; // optional, comment out and test
	if (empty($custom_email)){
		if($invoice_type == 'invoice'){
			$mail->MsgHTML(EMAIL_BODY_INVOICE);
		} else if($invoice_type == 'quote'){
			$mail->MsgHTML(EMAIL_BODY_QUOTE);
		} else if($invoice_type == 'receipt'){
			$mail->MsgHTML(EMAIL_BODY_RECEIPT);
		}
	} else {
		$mail->MsgHTML($custom_email);
	}

	$mail->AddAttachment(__DIR__ . "/../invoices/".$fileId.".pdf"); // attachment

	if(!$mail->Send()) {
		 //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mail->ErrorInfo.'</pre>'
	    ));
	} else {
	   echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Invoice has been successfully send to the customer'
		));
	}

}

// Create customer
if ($action == 'create_customer'){

	// invoice customer information
	// billing
	$customer_name = $_POST['customer_name'] ?? ''; // customer name
	$customer_email = $_POST['customer_email'] ?? ''; // customer email
	$customer_address_1 = $_POST['customer_address_1'] ?? ''; // customer address
	$customer_address_2 = $_POST['customer_address_2'] ?? ''; // customer address
	$customer_town = $_POST['customer_town'] ?? ''; // customer town
	$customer_county = $_POST['customer_county'] ?? ''; // customer county
	$customer_postcode = $_POST['customer_postcode'] ?? ''; // customer postcode
	$customer_phone = $_POST['customer_phone'] ?? ''; // customer phone number
	


	$query = "INSERT INTO store_customers (
					name,
					email,
					address_1,
					address_2,
					town,
					county,
					postcode,
					phone
				) VALUES (
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?
				);
			";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param(
		'ssssssss',
		$customer_name,$customer_email,$customer_address_1,$customer_address_2,$customer_town,$customer_county,$customer_postcode,
		$customer_phone);

	if($stmt->execute()){
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Customer has been created successfully!'
		));
	} else {
		// if unable to create invoice
		echo json_encode(array(
			'status' => 'Error',
			'message' => 'There has been an error, please try again.'
			// debug
			//'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
		));
	}

	//close database connection
	$mysqli->close();
}

// Create invoice
if ($action == 'create_invoice'){

	// invoice customer information
	// billing
	$customer_id = $_POST['customer'] ?? '';
	$customer_name = '';
	$customer_email = '';
	$customer_address_1 = '';
	$customer_address_2 = '';
	$customer_town = '';
	$customer_county = '';
	$customer_postcode = '';
	$customer_phone = '';

	if(!empty($customer_id)) {
		$stmt_cust = $mysqli->prepare("SELECT * FROM store_customers WHERE id = ?");
		$stmt_cust->bind_param("i", $customer_id);
		$stmt_cust->execute();
		$res_cust = $stmt_cust->get_result();
		if($row_cust = $res_cust->fetch_assoc()) {
			$customer_name = $row_cust['name'];
			$customer_email = $row_cust['email'];
			$customer_address_1 = $row_cust['address_1'];
			$customer_address_2 = $row_cust['address_2'];
			$customer_town = $row_cust['town'];
			$customer_county = $row_cust['county'];
			$customer_postcode = $row_cust['postcode'];
			$customer_phone = $row_cust['phone'];
		}
		$stmt_cust->close();
	}
	$invoice_number = $_POST['invoice_id']; // invoice number
	$custom_email = $_POST['custom_email'] ?? ''; // invoice custom email body
	$invoice_date = $_POST['invoice_date']; // invoice date
	$invoice_due_date = $_POST['invoice_due_date'] ?? ''; // invoice due date
	
	// Per-item FOC: FOC items contribute 0 to total, their original price saved in 'price' column
	$invoice_subtotal = 0;
	if (isset($_POST['invoice_product_price'])) {
		foreach($_POST['invoice_product_price'] as $k => $price) {
			$is_foc = isset($_POST['invoice_product_foc'][$k]) && $_POST['invoice_product_foc'][$k] == '1';
			if (!$is_foc) {
				$invoice_subtotal += floatval($price);
			}
		}
	}
	$invoice_discount = 0;
	$invoice_total = $invoice_subtotal;
	$invoice_vat = 0;
	$invoice_notes = $_POST['invoice_notes'] ?? ''; // Invoice notes
	$invoice_type = $_POST['invoice_type'] ?? 'invoice'; // Invoice type
	$invoice_status = $_POST['invoice_status'] ?? 'open'; // Invoice status

	// insert invoice into database
	$query = "INSERT INTO invoices (
					invoice,
					custom_email,
					invoice_date, 
					invoice_due_date, 
					subtotal, 
					discount, 
					vat, 
					total,
					notes,
					invoice_type,
					status
				) VALUES (
				  	'".$invoice_number."',
				  	'".$custom_email."',
				  	'".$invoice_date."',
				  	'".$invoice_due_date."',
				  	'".$invoice_subtotal."',
				  	'".$invoice_discount."',
				  	'".$invoice_vat."',
				  	'".$invoice_total."',
				  	'".$invoice_notes."',
				  	'".$invoice_type."',
				  	'".$invoice_status."'
			    );
			";
	// insert customer details into database
	$query .= "INSERT INTO customers (
					invoice,
					name,
					email,
					address_1,
					address_2,
					town,
					county,
					postcode,
					phone
				) VALUES (
					'".$invoice_number."',
					'".$customer_name."',
					'".$customer_email."',
					'".$customer_address_1."',
					'".$customer_address_2."',
					'".$customer_town."',
					'".$customer_county."',
					'".$customer_postcode."',
					'".$customer_phone."'
				);
			";

	// invoice product items - per-item FOC support
	// FOC checkboxes are sent as indexed: invoice_product_foc[0]=1, invoice_product_foc[2]=1, etc.
	// Non-FOC items won't have their index in the array
	foreach($_POST['invoice_product'] as $key => $value) {
	    $item_product  = $mysqli->real_escape_string($value);
	    $item_product_desc = isset($_POST['invoice_product_desc'][$key]) ? $mysqli->real_escape_string($_POST['invoice_product_desc'][$key]) : '';
	    $item_qty      = 1;
	    $item_price    = floatval($_POST['invoice_product_price'][$key]);
	    $is_foc        = isset($_POST['invoice_product_foc'][$key]) && $_POST['invoice_product_foc'][$key] == '1';
	    // Store original price in 'price', 0 in 'subtotal' for FOC items
	    // Store FOC flag: discount = item_price means FOC (used for detection on edit)
	    $item_discount = $is_foc ? $item_price : 0;
	    $item_subtotal = $is_foc ? 0 : $item_price;

	    $query .= "INSERT INTO invoice_items (
				invoice,
				product,
				product_desc,
				qty,
				price,
				discount,
				subtotal
			) VALUES (
				'".$invoice_number."',
				'".$item_product."',
				'".$item_product_desc."',
				'".$item_qty."',
				'".$item_price."',
				'".$item_discount."',
				'".$item_subtotal."'
			);
		";
	}

	header('Content-Type: application/json');

	// execute the query
	if($mysqli -> multi_query($query)){
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Invoice has been created successfully!'
		));

	} else {
		// if unable to create invoice
		echo json_encode(array(
			'status' => 'Error',
			'message' => 'There has been an error, please try again.'
			// debug
			//'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
		));
	}

	//close database connection
	$mysqli->close();

}

// Adding new product
if($action == 'delete_invoice') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM invoices WHERE invoice = '".$mysqli->real_escape_string($id)."';";
	$query .= "DELETE FROM customers WHERE invoice = '".$mysqli->real_escape_string($id)."';";
	$query .= "DELETE FROM invoice_items WHERE invoice = '".$mysqli->real_escape_string($id)."';";

	unlink(__DIR__ . '/../invoices/'.$id.'.pdf');

	if($mysqli -> multi_query($query)) {
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Product has been deleted successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	// close connection 
	$mysqli->close();

}

// Adding new product
if($action == 'update_customer') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$getID = $_POST['id']; // id

	// invoice customer information
	$customer_name = $_POST['customer_name'] ?? ''; // customer name
	$customer_email = $_POST['customer_email'] ?? ''; // customer email
	$customer_address_1 = $_POST['customer_address_1'] ?? ''; // customer address
	$customer_address_2 = $_POST['customer_address_2'] ?? ''; // customer address
	$customer_town = $_POST['customer_town'] ?? ''; // customer town
	$customer_county = $_POST['customer_county'] ?? ''; // customer county
	$customer_postcode = $_POST['customer_postcode'] ?? ''; // customer postcode
	$customer_phone = $_POST['customer_phone'] ?? ''; // customer phone number

	// the query
	$query = "UPDATE store_customers SET
				name = ?,
				email = ?,
				address_1 = ?,
				address_2 = ?,
				town = ?,
				county = ?,
				postcode = ?,
				phone = ?
				WHERE id = ?

			";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param(
		'sssssssss',
		$customer_name,$customer_email,$customer_address_1,$customer_address_2,$customer_town,$customer_county,$customer_postcode,
		$customer_phone,$getID);

	//execute the query
	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Customer has been updated successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	//close database connection
	$mysqli->close();
	
}



// Adding new product
if($action == 'update_invoice') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$id = $_POST["update_id"];

	// the query
	$query = "DELETE FROM invoices WHERE invoice = '".$mysqli->real_escape_string($id)."';";
	$query .= "DELETE FROM customers WHERE invoice = '".$mysqli->real_escape_string($id)."';";
	$query .= "DELETE FROM invoice_items WHERE invoice = '".$mysqli->real_escape_string($id)."';";

	$pdf_path = __DIR__ . '/../invoices/'.$id.'.pdf';
	if (file_exists($pdf_path)) {
		@unlink($pdf_path);
	}

	$customer_id = $_POST['customer'] ?? '';
	$customer_name = '';
	$customer_email = '';
	$customer_address_1 = '';
	$customer_address_2 = '';
	$customer_town = '';
	$customer_county = '';
	$customer_postcode = '';
	$customer_phone = '';

	if(!empty($customer_id)) {
		$stmt_cust = $mysqli->prepare("SELECT * FROM store_customers WHERE id = ?");
		$stmt_cust->bind_param("i", $customer_id);
		$stmt_cust->execute();
		$res_cust = $stmt_cust->get_result();
		if($row_cust = $res_cust->fetch_assoc()) {
			$customer_name = $row_cust['name'];
			$customer_email = $row_cust['email'];
			$customer_address_1 = $row_cust['address_1'];
			$customer_address_2 = $row_cust['address_2'];
			$customer_town = $row_cust['town'];
			$customer_county = $row_cust['county'];
			$customer_postcode = $row_cust['postcode'];
			$customer_phone = $row_cust['phone'];
		}
		$stmt_cust->close();
	}
	

	$invoice_number = $_POST['invoice_id']; // invoice number
	$custom_email = $_POST['custom_email'] ?? ''; // invoice custom email body
	$invoice_date = $_POST['invoice_date']; // invoice date
	$invoice_due_date = $_POST['invoice_due_date'] ?? ''; // invoice due date
	
	// Per-item FOC: FOC items contribute 0 to total
	$invoice_subtotal = 0;
	if (isset($_POST['invoice_product_price'])) {
		foreach($_POST['invoice_product_price'] as $k => $price) {
			$is_foc = isset($_POST['invoice_product_foc'][$k]) && $_POST['invoice_product_foc'][$k] == '1';
			if (!$is_foc) {
				$invoice_subtotal += floatval($price);
			}
		}
	}
	$invoice_discount = 0;
	$invoice_total = $invoice_subtotal;
	$invoice_vat = 0;
	$invoice_notes = $_POST['invoice_notes'] ?? '';
	$invoice_type = $_POST['invoice_type'] ?? 'invoice';
	$invoice_status = $_POST['invoice_status'] ?? 'open';

	// insert invoice into database
	$query .= "INSERT INTO invoices (
					invoice, 
					custom_email,
					invoice_date, 
					invoice_due_date, 
					subtotal, 
					discount, 
					vat, 
					total,
					notes,
					invoice_type,
					status
				) VALUES (
				  	'".$invoice_number."',
				  	'".$custom_email."',
				  	'".$invoice_date."',
				  	'".$invoice_due_date."',
				  	'".$invoice_subtotal."',
				  	'".$invoice_discount."',
				  	'".$invoice_vat."',
				  	'".$invoice_total."',
				  	'".$invoice_notes."',
				  	'".$invoice_type."',
				  	'".$invoice_status."'
			    );
			";
	// insert customer details into database
	$query .= "INSERT INTO customers (
					invoice,
					name,
					email,
					address_1,
					address_2,
					town,
					county,
					postcode,
					phone
				) VALUES (
					'".$invoice_number."',
					'".$customer_name."',
					'".$customer_email."',
					'".$customer_address_1."',
					'".$customer_address_2."',
					'".$customer_town."',
					'".$customer_county."',
					'".$customer_postcode."',
					'".$customer_phone."'
				);
			";

	// invoice product items - per-item FOC support
	foreach($_POST['invoice_product'] as $key => $value) {
	    $item_product  = $mysqli->real_escape_string($value);
	    $item_product_desc = isset($_POST['invoice_product_desc'][$key]) ? $mysqli->real_escape_string($_POST['invoice_product_desc'][$key]) : '';
	    $item_qty      = 1;
	    $item_price    = floatval($_POST['invoice_product_price'][$key]);
	    $is_foc        = isset($_POST['invoice_product_foc'][$key]) && $_POST['invoice_product_foc'][$key] == '1';
	    $item_discount = $is_foc ? $item_price : 0;
	    $item_subtotal = $is_foc ? 0 : $item_price;

	    $query .= "INSERT INTO invoice_items (
				invoice,
				product,
				product_desc,
				qty,
				price,
				discount,
				subtotal
			) VALUES (
				'".$invoice_number."',
				'".$item_product."',
				'".$item_product_desc."',
				'".$item_qty."',
				'".$item_price."',
				'".$item_discount."',
				'".$item_subtotal."'
			);
		";
	}

	header('Content-Type: application/json');

	if($mysqli -> multi_query($query)) {
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Product has been updated successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	// close connection 
	$mysqli->close();

}


// Login to system
if($action == 'login') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	session_start();

    extract($_POST);

    $username = mysqli_real_escape_string($mysqli,$_POST['username']);
    $pass_encrypt = md5(mysqli_real_escape_string($mysqli,$_POST['password']));

    $query = "SELECT * FROM `users` WHERE username='$username' AND `password` = '$pass_encrypt'";

    $results = mysqli_query($mysqli,$query) or die (mysqli_error());
    $count = mysqli_num_rows($results);

    if($count!="") {
		$row = $results->fetch_assoc();

		$_SESSION['login_username'] = $row['username'];

		// processing remember me option and setting cookie with long expiry date
		if (isset($_POST['remember'])) {	
			session_set_cookie_params('604800'); //one week (value in seconds)
			session_regenerate_id(true);
		}  
		
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Login was a success! Transfering you to the system now, hold tight!'
		));
    } else {
    	echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'Login incorrect, does not exist or simply a problem! Try again!'
	    ));
    }
}


// Adding new user
if($action == 'add_user') {

	$user_name = $_POST['name'];
	$user_username = $_POST['username'];
	$user_email = $_POST['email'];
	$user_phone = $_POST['phone'];
	$user_password = $_POST['password'];

	//our insert query query
	$query  = "INSERT INTO users
				(
					name,
					username,
					email,
					phone,
					password
				)
				VALUES (
					?,
					?, 
                	?,
                	?,
                	?
                );
              ";

    header('Content-Type: application/json');

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	$user_password = md5($user_password);
	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('sssss',$user_name,$user_username,$user_email,$user_phone,$user_password);

	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'User has been added successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	//close database connection
	$mysqli->close();
}

// Update product
if($action == 'update_user') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	// user information
	$getID = $_POST['id']; // id
	$name = $_POST['name']; // name
	$username = $_POST['username']; // username
	$email = $_POST['email']; // email
	$phone = $_POST['phone']; // phone
	$password = $_POST['password']; // password

	if($password == ''){
		// the query
		$query = "UPDATE users SET
					name = ?,
					username = ?,
					email = ?,
					phone = ?
				 WHERE id = ?
				";
	} else {
		// the query
		$query = "UPDATE users SET
					name = ?,
					username = ?,
					email = ?,
					phone = ?,
					password =?
				 WHERE id = ?
				";
	}

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	if($password == ''){
		/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
		$stmt->bind_param(
			'sssss',
			$name,$username,$email,$phone,$getID
		);
	} else {
		$password = md5($password);
		/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
		$stmt->bind_param(
			'ssssss',
			$name,$username,$email,$phone,$password,$getID
		);
	}

	//execute the query
	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'User has been updated successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	//close database connection
	$mysqli->close();
	
}

// Delete User
if($action == 'delete_user') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM users WHERE id = ?";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('s',$id);

	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'User has been deleted successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	// close connection 
	$mysqli->close();

}

// Delete User
if($action == 'delete_customer') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM store_customers WHERE id = ?";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('s',$id);

	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Customer has been deleted successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	// close connection 
	$mysqli->close();

}

?>