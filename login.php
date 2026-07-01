<?php
/*******************************************************************************
* Invoice Management System                                                   *
* Version: 1.0                                                                *
* Developer: Abhishek Raj                                                     *
*******************************************************************************/
include('includes/header-login.php');
include('includes/functions.php');
session_start();

// Connect to the database
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

// output any connection error
if ($mysqli->connect_error) {
    die('Error : ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
}

if (isset($_POST['username']) && isset($_POST['password']) && $_POST['username'] != "" && $_POST['password'] != "") {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Ambil user berdasarkan username
    $query = $mysqli->query("SELECT * FROM `users` WHERE username='$username'");

    if ($query && $query->num_rows > 0) {
        $row = $query->fetch_assoc();

        // Verifikasi password hash
        if (password_verify($password, $row['password'])) {
            $_SESSION['login_username'] = $row['username'];
            echo 1;
        } else {
            echo 0;
        }
    } else {
        echo 0;
    }

} else {
    header("Location: index.php");
    exit();
}
?>

<?php include('footer.php'); ?>
