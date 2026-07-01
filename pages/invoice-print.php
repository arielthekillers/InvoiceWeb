<?php
include('../includes/config.php');

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
    die("Invoice not found.");
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
$exp_date = explode('-', $invoice_date_raw);
if (count($exp_date) == 3) {
    $invoice_date = (int)$exp_date[2] . ' ' . $months[(int)$exp_date[1]] . ' ' . $exp_date[0];
} else {
    $invoice_date = $invoice_date_raw;
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print Invoice - <?php echo htmlspecialchars($invoice_number); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px; /* Smaller font */
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 30px; /* Reduced padding */
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-weight-bold { font-weight: bold; }
        
        .header { margin-bottom: 50px; } /* Increased margin */
        .header h1 {
            font-size: 20px; /* Smaller header */
            margin: 0;
            letter-spacing: 1px;
        }
        .header p {
            margin: 2px 0;
            color: #555;
            font-size: 13px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px; /* Reduced margin */
        }
        .info-block { width: 48%; }
        .info-block h4 {
            font-size: 12px;
            margin: 0 0 5px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
            padding-bottom: 0px;
            font-size: 12px;
        }
        .info-table td:first-child { width: 50px; font-weight: 600; }
        .info-table td:nth-child(2) { width: 10px; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px; /* Reduced margin */
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 6px 10px; /* Reduced padding */
        }
        .items-table th {
            background-color: #a0a0a0;
            color: #fff;
            text-align: left;
            font-weight: bold;
            font-size: 13px;
        }
        .items-table th.text-center { text-align: center; }
        
        /* Specific column widths */
        .col-no { width: 5%; text-align: center; }
        .col-uraian { width: 70%; }
        .col-biaya { width: 25%; text-align: right; }

        .uraian-content {
            margin: 0;
            white-space: pre-line; /* Renders line breaks properly */
        }

        .total-row td {
            font-weight: bold;
        }

        .rekening {
            margin-top: 20px; /* Reduced margin */
        }
        .rekening h4 {
            margin: 0 0 3px 0;
            font-size: 13px;
        }
        .rekening p {
            margin: 0;
            font-style: italic;
        }

        .footer-note {
            margin-top: 30px; /* Reduced margin */
            font-size: 12px;
            color: #666;
        }

        @media print {
            body { padding: 0; }
            .items-table th {
                background-color: #a0a0a0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="header text-center">
        <h1>INVOICE</h1>
        <p><span class="font-weight-bold">No.</span> <?php echo htmlspecialchars($invoice_number); ?></p>
        <p><span class="font-weight-bold">Tanggal:</span> <?php echo htmlspecialchars($invoice_date); ?></p>
    </div>

    <div class="info-section">
        <div class="info-block">
            <h4>Dibuat Oleh:</h4>
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars(COMPANY_NAME); ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars(COMPANY_ADDRESS_1); ?></td>
                </tr>
                <tr>
                    <td>Telepon</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars(COMPANY_NUMBER); ?></td>
                </tr>
            </table>
        </div>
        <div class="info-block">
            <h4>Ditujukan Kepada:</h4>
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($customer_name); ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><?php 
                        $full_address = array_filter([$customer_address_1, $customer_address_2, $customer_town]);
                        echo htmlspecialchars(implode(', ', $full_address)); 
                    ?></td>
                </tr>
                <tr>
                    <td>Telepon</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($customer_phone); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <h3 style="margin-bottom: 10px;">Rincian Pembayaran</h3>

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
                    <div class="uraian-content"><?php echo htmlspecialchars($item['product']); ?></div>
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
                <td class="text-right"><?php echo number_format($invoice_total, 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="rekening">
        <h4>Rekening Pembayaran</h4>
        <p><?php echo htmlspecialchars(PAYMENT_DETAILS); ?></p>
    </div>

    <div class="footer-note">
        Catatan : Invoice ini sah tanpa tanda tangan basah atau materai.
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
        window.addEventListener("afterprint", function(event) {
            window.close();
            // Fallback jika browser memblokir penutupan window (misal bukan dibuka via script)
            setTimeout(function() {
                window.history.back();
            }, 500);
        });
    </script>
</body>
</html>
