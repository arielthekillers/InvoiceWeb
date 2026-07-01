<?php
$m = new mysqli('localhost', 'root', '', 'invoicemgsys'); 
$res = $m->query('DESCRIBE customers'); 
while($r = $res->fetch_assoc()) { 
    echo $r['Field']."\n"; 
}
