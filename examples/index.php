<?php
require_once __DIR__ . '/class/Kamoney.class.php';

// // Public
// // GET Currecy list
// Kamoney::public_currency();

// // GET System Info
// Kamoney::public_system_info();

// Private
Kamoney::$public_key = 'cd085610b37ab8fba8435f3fd23eabf1';
Kamoney::$secret_key = '527a1995babf9888c4efa4b27a455599';

// Create merchant
$asset = 'BTC';
$network = 'BTC';
$amount = 10; // in R$
$email_client = "claudecigoularte@gmail.com";
$url_callback = "https://webhook.site/fba07365-0284-4ee1-a122-893d921204e9";

$create = Kamoney::merchant_create($asset, $network, $amount, $email_client, $url_callback);
exit(var_dump($create));
