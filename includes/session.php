<?php 
	include_once(__DIR__ . '/config.php');

	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	session_start();

	$check = isset($_SESSION['login_username']) ? $_SESSION['login_username'] : null;

	if(!$check) {
	    header("Location: " . BASE_URL . "index.php");
	    exit;
	}
?>