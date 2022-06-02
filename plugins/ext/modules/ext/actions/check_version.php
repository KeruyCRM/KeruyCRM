<?php

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://www.keruy.com.ua/current_version/ext_version.txt");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


$response = curl_exec($ch);

if (strlen($response) < 10) {
    $plugin_ext_current_version = $response;
}

curl_close($ch);