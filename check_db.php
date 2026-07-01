<?php
include('includes/config.php');
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

echo "STORE_CUSTOMERS:\n";
$res = $mysqli->query("SELECT * FROM store_customers");
while ($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - " . $row['name'] . "\n";
}
