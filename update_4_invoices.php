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

function update_invoice_via_curl($post_data) {
    $ch = curl_init('http://localhost/invoice/includes/response.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    $response = curl_exec($ch);
    curl_close($ch);
    echo "Response for " . $post_data['update_id'] . ": " . $response . "\n";
}

// 1. INV012926004 (29 Januari 2026)
$item2_desc = "Pengembangan Sistem Informasi Biro Pengajaran KMI Darussalam Bogor (KMI App) https://darussalambogor.ac.id/kmi :\nDashboard\n- Ringkasan informasi & akses cepat fitur utama.\nMaster Data (Admin)\n- Pengelolaan data pelajaran, pengajar, dan kelas.\nManajemen Jadwal\n- Jadwal pelajaran (KBM).\n- Jadwal Syeikh Diwan & piket keliling.\n- Absensi pengajar & laporan piket.\n- Akses jadwal mengajar masing-masing pengajar.\nTanqih Idad (Persiapan Mengajar)\n- Verifikasi persiapan mengajar.\n- Filter status & laporan rekap.\nKoreksi Ujian\n- Input dan konversi nilai ujian.\n- Monitoring status koreksi.\nKeamanan Sistem\n- Login multi-role (Admin & Pengajar).\n- Logout sistem.";

update_invoice_via_curl([
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
]);

// 2. INV210426005 (21 April 2026)
$item1_desc_4 = "Pengembangan Sistem Informasi Biro Pengajaran KMI Darussalam Bogor (KMI App) https://darussalambogor.ac.id/kmi\n- Upgrade Aplikasi ke versi 2\n- Refraktori Database\n- Modul Tahun Ajaran\n- Modul Data Santri\n- Update Modul Kelas\n- Profil Kelas\n- Migrasi Data Santri dari Spreadsheet ke Database\n- Penyesuaian fitur aplikasi menggunakan prinsip tahun ajaran.";

update_invoice_via_curl([
    'action' => 'update_invoice',
    'update_id' => 'INV210426005',
    'invoice_id' => 'INV210426005',
    'invoice_date' => '2026-04-21',
    'customer' => '1',
    'invoice_product' => [$item1_desc_4],
    'invoice_product_price' => [1500000],
    'invoice_product_foc' => [0]
]);

// 3. INV18032600 (18 Maret 2026)
$item1_desc_3 = "Pengembangan Sistem Informasi Biro Pengajaran KMI Darussalam Bogor (KMI App) https://darussalambogor.ac.id/kmi\n- Aplikasi TV Showcase Module TV Showcase\n- Module Update Profile\n- Module Settings TV Showcase (Audio, Jam Pelajaran, Quotes)\n- Update Modul Koreksi Ujian\n- Penyesuaian UI/UX dan Perbaikan Navigas\n- Optimasi Database";

update_invoice_via_curl([
    'action' => 'update_invoice',
    'update_id' => 'INV18032600',
    'invoice_id' => 'INV18032600',
    'invoice_date' => '2026-03-18',
    'customer' => '1',
    'invoice_product' => [$item1_desc_3],
    'invoice_product_price' => [1500000],
    'invoice_product_foc' => [0]
]);

// 4. INV02052600 (02 Mei 2026)
$item1_desc_2 = "Pengembangan Sistem Informasi Biro Pengajaran KMI Darussalam Bogor (KMI App) https://darussalambogor.ac.id/kmi\n- Update module panitia ujian (Aktivasi sesi ujian)\n- Update Module module koreksi ujian (Implementasi bayanat, ujian lisan, role access)\n- Update modul Data Santri dan Pengajar (Sort Data, Trash Data)\n- Update TV Showcase (Counting Data)\n- Penyesuaian UI/UX Data Pengajar\n- Optimasi Database (Trash Data)";

update_invoice_via_curl([
    'action' => 'update_invoice',
    'update_id' => 'INV02052600',
    'invoice_id' => 'INV02052600',
    'invoice_date' => '2026-05-02',
    'customer' => '1',
    'invoice_product' => [$item1_desc_2],
    'invoice_product_price' => [1500000],
    'invoice_product_foc' => [0]
]);

echo "Done.\n";
