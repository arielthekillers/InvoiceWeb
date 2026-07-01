<?php
include('../includes/header.php');
include('../includes/functions.php');

$getID = $_GET['id'];

// output any connection error
if ($mysqli->connect_error) {
	die('Error : ('.$mysqli->connect_errno .') '. $mysqli->connect_error);
}

// the query
$query = "SELECT * FROM users WHERE id = '" . $mysqli->real_escape_string($getID) . "'";

$result = mysqli_query($mysqli, $query);

// mysqli select query
if($result) {
	while ($row = mysqli_fetch_assoc($result)) {
		$name = $row['name']; // name
		$username = $row['username']; // username
		$email = $row['email']; // email address
		$phone = $row['phone']; // phone number
		$password = $row['password']; // password
	}
}

/* close connection */
$mysqli->close();
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Edit User</h2>
        <hr>
    </div>
</div>

<div id="response" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <div class="message"></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
                        
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Editing User (<?php echo $getID; ?>)</h5>
            </div>
            <div class="card-body">
                <form method="post" id="update_user">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="id" value="<?php echo $getID; ?>">

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Name</label>
                            <input type="text" class="form-control required" name="name" placeholder="Name" value="<?php echo $name; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Username</label>
                            <input type="text" class="form-control required" name="username" placeholder="Enter username" value="<?php echo $username; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Email Address</label>
                            <input type="email" class="form-control required" name="email" placeholder="Enter user's email address" value="<?php echo $email; ?>">
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Phone Number</label>
                            <input type="text" class="form-control" name="phone" placeholder="Enter user's phone number" value="<?php echo $phone; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">New Password (Optional)</label>
                            <input type="password" class="form-control required" name="password" id="password" placeholder="Leave empty to keep current password">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 text-end">
                            <button type="submit" id="action_update_user" class="btn btn-success" data-loading-text="Editing...">
                                <i class="bi bi-check-circle me-1"></i> Edit User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include('../includes/footer.php');
?>
