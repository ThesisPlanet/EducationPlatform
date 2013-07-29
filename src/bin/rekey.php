<?php
require_once('../cron/init.php');
$encryption = new \TP\Cryptography\Rijndael256();

echo "Please provide a password.\n";
$handle = fopen("php://stdin", "r");
$p = fgets($handle);
echo "Please provide a salt\n";
$handle = fopen("php://stdin", "r");
$s = fgets($handle);
echo "How many times would you like to iterate (1000 or higher recommended)?\n";
$handle = fopen("php://stdin", "r");
$c = (int) fgets($handle);
// 8*32 = 256 bits.
$kl = 32;
$key = $encryption->pbkdf2($p, $s, $c, $kl);
echo "Your key:\n";
echo base64_encode($key);
