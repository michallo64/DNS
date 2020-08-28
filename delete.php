<?php

if (!empty($_POST)) {
    $ch = curl_init();
    session_start();

    $domain = 'php-assignment-8.ws';
    $time = time();
    $method = 'DELETE';
    $path = '/v1/user/self/zone/' . $domain . '/record/' . $_POST['id'];
    $api = 'https://rest.websupport.sk';
    $apiKey = '8b2fb78b-9e7c-47d4-8676-11f19e9307c3';
    $secret = '6327b8b782d93ec5a6b04164dd0561f76ca6b1e6';
    $canonicalRequest = sprintf('%s %s %s', $method, $path, $time);
    $signature = hash_hmac('sha1', $canonicalRequest, $secret);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, sprintf('%s:%s', $api, $path));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':' . $signature);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Date: ' . gmdate('Ymd\THis\Z', $time),
    ]);

    $response = curl_exec($ch);
    $decoded = json_decode($response);
    curl_close($ch);
    $_SESSION['deleted'] = $decoded->status;
    header('Location: index.php');
}

