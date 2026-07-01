<?php
include('../includes/header.php');
include('../includes/functions.php');

$getID = $_GET['id'] ?? '';

// Connect to the database
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

// Fetch invoice and customer data
$query = "SELECT i.*, c.*
          FROM invoices i 
          JOIN customers c ON c.invoice = i.invoice
          WHERE i.invoice = '" . $mysqli->real_escape_string($getID) . "'";

$result = mysqli_query($mysqli, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<div class='alert alert-danger'>Invoice not found.</div>";
    include('../includes/footer.php');
    exit;
}

$row = mysqli_fetch_assoc($result);

$invoice_number = $row['invoice'];
$invoice_date_raw = $row['invoice_date'];
$invoice_total = $row['total'];
$customer_name = $row['name'];
$customer_address_1 = $row['address_1'];
$customer_address_2 = $row['address_2'];
$customer_town = $row['town'];
$customer_phone = $row['phone'];

// Indonesian date formatting
$months = array(
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
);
$exp_date = explode('/', $invoice_date_raw);
if (count($exp_date) == 3) {
    $invoice_date = (int)$exp_date[0] . ' ' . $months[(int)$exp_date[1]] . ' ' . $exp_date[2];
} else {
    $exp_date_dash = explode('-', $invoice_date_raw);
    if(count($exp_date_dash) == 3) {
        $invoice_date = (int)$exp_date_dash[2] . ' ' . $months[(int)$exp_date_dash[1]] . ' ' . $exp_date_dash[0];
    } else {
        $invoice_date = $invoice_date_raw;
    }
}

// Fetch items
$query_items = "SELECT * FROM invoice_items WHERE invoice = '" . $mysqli->real_escape_string($getID) . "'";
$result_items = mysqli_query($mysqli, $query_items);
$items = [];
if ($result_items) {
    while ($item = mysqli_fetch_assoc($result_items)) {
        $items[] = $item;
    }
}
$mysqli->close();
?>

<style>
    /* Screen Styles */
    .invoice-box {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    .invoice-header-title { margin-bottom: 40px; }
    .invoice-header-title h1 { margin: 0; font-size: 24px; font-weight: bold; }
    .invoice-header-title p { margin: 0; color: #666; font-size: 14px; }
    .info-table td { padding: 0px 0; font-size: 13px; }
    .info-table td:first-child { font-weight: 600; width: 60px; }
    .info-table td:nth-child(2) { width: 15px; }
    
    .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; }
    .items-table th { background: #f8f9fa; font-weight: bold; }
    .items-table th.text-center { text-align: center; }
    
    .col-no { width: 5%; text-align: center; }
    .col-uraian { width: 70%; }
    .col-biaya { width: 25%; text-align: right; }
    .uraian-content { white-space: pre-line; }
    .total-row td { font-weight: bold; }
    
    .rekening { margin-top: 30px; }
    .rekening h4 { margin: 0 0 5px 0; font-size: 15px; font-weight: bold; }
    .footer-note { margin-top: 40px; font-size: 13px; color: #777; }

    /* Print Styles */
    @media print {
        body * {
            visibility: hidden;
        }
        .invoice-box, .invoice-box * {
            visibility: visible;
        }
        .invoice-box {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            box-shadow: none;
        }
        .no-print {
            display: none !important;
        }
        .items-table th {
            background-color: #a0a0a0 !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            border-color: #000 !important;
        }
        .items-table td {
            border-color: #000 !important;
        }
    }
</style>

<script>
    // Change document title to invoice number so when printing, 
    // the default PDF filename is the invoice number.
    document.title = "<?php echo htmlspecialchars($invoice_number); ?>";
</script>

<div class="row mb-3 no-print">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <a href="invoice-list.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
        <div>
            <a href="invoice-edit.php?id=<?php echo $invoice_number; ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
            <button class="btn btn-info btn-sm text-white" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
            <button class="btn btn-danger btn-sm delete-invoice-detail" data-invoice-id="<?php echo $invoice_number; ?>"><i class="bi bi-trash"></i> Hapus</button>
        </div>
    </div>
</div>

<div class="invoice-box">
    <div class="text-center invoice-header-title">
        <h1>INVOICE</h1>
        <p><strong>No.</strong> <?php echo htmlspecialchars($invoice_number); ?></p>
        <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($invoice_date); ?></p>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h5 style="font-size: 15px; font-weight: bold; margin-bottom:10px;">Dibuat Oleh:</h5>
            <table class="info-table">
                <tr><td>Nama</td><td>:</td><td><?php echo htmlspecialchars(COMPANY_NAME); ?></td></tr>
                <tr><td>Alamat</td><td>:</td><td><?php echo htmlspecialchars(COMPANY_ADDRESS_1); ?></td></tr>
                <tr><td>Telepon</td><td>:</td><td><?php echo htmlspecialchars(COMPANY_NUMBER); ?></td></tr>
            </table>
        </div>
        <div class="col-6">
            <h5 style="font-size: 15px; font-weight: bold; margin-bottom:10px;">Ditujukan Kepada:</h5>
            <table class="info-table">
                <tr><td>Nama</td><td>:</td><td><?php echo htmlspecialchars($customer_name); ?></td></tr>
                <tr><td>Alamat</td><td>:</td><td>
                    <?php 
                        $full_address = array_filter([$customer_address_1, $customer_address_2, $customer_town]);
                        echo htmlspecialchars(implode(', ', $full_address)); 
                    ?>
                </td></tr>
                <tr><td>Telepon</td><td>:</td><td><?php echo htmlspecialchars($customer_phone); ?></td></tr>
            </table>
        </div>
    </div>

    <h5 style="font-size: 16px; font-weight: bold; margin-bottom:10px;">Rincian Pembayaran</h5>
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-uraian">Uraian</th>
                <th class="col-biaya">Biaya (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($items as $item): 
            ?>
            <tr>
                <td class="col-no align-top"><?php echo $no++; ?></td>
                <td class="col-uraian align-top">
                    <div class="uraian-content fw-bold"><?php echo htmlspecialchars($item['product']); ?></div>
                    <?php if (!empty($item['product_desc'])): ?>
                    <ul style="margin: 3px 0 0 15px; padding: 0; font-size: 11px; color: #555;">
                        <?php 
                        $desc_value_clean = str_replace('\n', "\n", $item['product_desc']);
                        $subs = explode("\n", $desc_value_clean);
                        foreach($subs as $sub) {
                            if (trim($sub) !== '') {
                                echo '<li>' . htmlspecialchars(trim($sub)) . '</li>';
                            }
                        }
                        ?>
                    </ul>
                    <?php endif; ?>
                </td>
                <td class="col-biaya align-top">
                    <?php 
                    if ($item['price'] == 0 || strpos(strtolower($item['product']), 'gratis') !== false) {
                        echo "0"; 
                    } else {
                        echo number_format($item['price'], 0, ',', '.');
                    }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="2" class="text-center">Total</td>
                <td class="text-end" style="text-align: right;"><?php echo number_format($invoice_total, 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="rekening">
        <h4>Rekening Pembayaran</h4>
        <p class="mb-0" style="font-style: italic;"><?php echo PAYMENT_DETAILS; ?></p>
    </div>

    <div class="footer-note">
        Catatan : Invoice ini sah tanpa tanda tangan basah atau materai.
    </div>
</div>

<!-- Modal Delete specific for Detail Page -->
<div id="delete_invoice_detail" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Delete Invoice</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-muted">
        <p>Are you sure you want to delete this invoice? This action cannot be undone.</p>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="delete_detail_confirm">Delete</button>
      </div>
    </div>
  </div>
</div>

<?php
include('../includes/footer.php');
?>
