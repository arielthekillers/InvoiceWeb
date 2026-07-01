<?php
$m = new mysqli('localhost', 'root', '', 'invoicemgsys');
$res = $m->query("SELECT product FROM invoice_items WHERE invoice='INV012926004'");
while ($row = $res->fetch_assoc()) {
    echo $row['product'] . "\n---\n";
}
