<?php
include('includes/config.php');

$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

// Ensure the invoices exist, if not, create dummy ones so update_invoice works
$invoices_to_check = ['INV210426005', 'INV18032600', 'INV02052600', 'INV012926004'];
foreach ($invoices_to_check as $inv) {
    $res = $mysqli->query("SELECT * FROM invoices WHERE invoice='$inv'");
    if ($res->num_rows == 0) {
        $mysqli->query("INSERT INTO invoices (invoice, invoice_date, subtotal, total, status) VALUES ('$inv', '2026-01-01', 0, 0, 'open')");
    }
}

// 1. INV012926004 (29 Januari 2026)
$item2_desc = "Pengembangan Sistem Informasi Biro Pengajaran KMI Darussalam Bogor (KMI App) https://darussalambogor.ac.id/kmi :\nDashboard\n- Ringkasan informasi & akses cepat fitur utama.\nMaster Data (Admin)\n- Pengelolaan data pelajaran, pengajar, dan kelas.\nManajemen Jadwal\n- Jadwal pelajaran (KBM).\n- Jadwal Syeikh Diwan & piket keliling.\n- Absensi pengajar & laporan piket.\n- Akses jadwal mengajar masing-masing pengajar.\nTanqih Idad (Persiapan Mengajar)\n- Verifikasi persiapan mengajar.\n- Filter status & laporan rekap.\nKoreksi Ujian\n- Input dan konversi nilai ujian.\n- Monitoring status koreksi.\nKeamanan Sistem\n- Login multi-role (Admin & Pengajar).\n- Logout sistem.";

$_POST = [
    'action' => 'update_invoice',
    'update_id' => 'INV012926004',
    'invoice_id' => 'INV012926004',
    'invoice_date' => '2026-01-29',
    'customer' => '1',
    'invoice_product' => [
        'Sewa Hosting & Domain',
        $item2_desc,
        'Setup Server dan Database'
    ],
    'invoice_product_price' => [0, 1000000, 0],
    'invoice_product_foc' => [1, 0, 1]
];

$id = $_POST["update_id"];

// the query
$query = "DELETE FROM invoices WHERE invoice = '".$mysqli->real_escape_string($id)."';";
$query .= "DELETE FROM customers WHERE invoice = '".$mysqli->real_escape_string($id)."';";
$query .= "DELETE FROM invoice_items WHERE invoice = '".$mysqli->real_escape_string($id)."';";

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

$query .= "INSERT INTO invoices (
                invoice, 
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
$query .= "INSERT INTO customers (
                invoice,
                custom_email,
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
                '".$custom_email."',
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

foreach($_POST['invoice_product'] as $key => $value) {
    $item_product  = $mysqli->real_escape_string($value);
    $item_qty      = 1;
    $item_price    = floatval($_POST['invoice_product_price'][$key]);
    $is_foc        = isset($_POST['invoice_product_foc'][$key]) && $_POST['invoice_product_foc'][$key] == '1';
    $item_discount = $is_foc ? $item_price : 0;
    $item_subtotal = $is_foc ? 0 : $item_price;

    $query .= "INSERT INTO invoice_items (
            invoice,
            product,
            qty,
            price,
            discount,
            subtotal
        ) VALUES (
            '".$invoice_number."',
            '".$item_product."',
            '".$item_qty."',
            '".$item_price."',
            '".$item_discount."',
            '".$item_subtotal."'
        );
    ";
}

if ($mysqli->multi_query($query)) {
    do {
        if ($res = $mysqli->store_result()) {
            $res->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());
}
if ($mysqli->error) {
    echo "SQL ERROR: " . $mysqli->error . "\n";
} else {
    echo "SUCCESS\n";
}
