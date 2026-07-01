<?php
// Debugging
ini_set('error_reporting', E_ALL);
define('BASE_URL', 'http://localhost/invoice/');

// DATABASE INFORMATION
define('DATABASE_HOST', 'localhost');
define('DATABASE_NAME', 'invoicemgsys');
define('DATABASE_USER', 'root');
define('DATABASE_PASS', '');

// COMPANY INFORMATION (Info Pembuat Invoice)
define('COMPANY_LOGO', __DIR__ . '/../assets/images/logo.png');
define('COMPANY_LOGO_WIDTH', '300');
define('COMPANY_LOGO_HEIGHT', '90');
define('COMPANY_NAME','Ahmad Darul Arqam');
define('COMPANY_ADDRESS_1','Bontang');
define('COMPANY_ADDRESS_2','Kalimantan Timur');
define('COMPANY_ADDRESS_3','Indonesia');
define('COMPANY_COUNTY','ID');
define('COMPANY_POSTCODE','75311');

define('COMPANY_NUMBER','+6281359774765'); // No. Telepon
define('COMPANY_VAT', ''); // Tidak digunakan

// EMAIL DETAILS
define('EMAIL_FROM', 'ahmaddarularqam@gmail.com');
define('EMAIL_NAME', 'Ahmad Darul Arqam');
define('EMAIL_SUBJECT', 'Invoice dari Ahmad Darul Arqam');
define('EMAIL_BODY_INVOICE', 'Terlampir invoice untuk pekerjaan yang telah selesai. Mohon untuk segera diproses pembayarannya. Terima kasih.');
define('EMAIL_BODY_QUOTE', 'Terlampir penawaran harga untuk pekerjaan yang diminta.');
define('EMAIL_BODY_RECEIPT', 'Terlampir tanda terima pembayaran. Terima kasih atas kepercayaan Anda.');

// OTHER SETTINGS
define('INVOICE_PREFIX', 'INV'); // Prefix nomor invoice
define('INVOICE_INITIAL_VALUE', '1'); // Nomor awal invoice
define('INVOICE_THEME', '#1a5276'); // Warna tema PDF invoice
define('TIMEZONE', 'Asia/Makassar'); // WIB+1 = WITA (Bontang, Kalimantan Timur)
define('DATE_FORMAT', 'DD/MM/YYYY');
define('CURRENCY', 'Rp '); // Rupiah
define('ENABLE_VAT', false); // Tidak menggunakan PPN/VAT
define('VAT_INCLUDED', false);
define('VAT_RATE', '0');

define('PAYMENT_DETAILS', 'Bank Mandiri<br>No. Rekening: 1480014663770<br>a.n. Ahmad Darul Arqam');
define('FOOTER_NOTE', 'Invoice ini sah tanpa tanda tangan basah atau materai.');

// CONNECT TO THE DATABASE
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

?>