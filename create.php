<?php

if (!empty($_POST)) {
    $ch = curl_init();
    session_start();

    $domain = 'php-assignment-8.ws';
    $time = time();
    $method = 'POST';
    $path = '/v1/user/self/zone/' . $domain . '/record';
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
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Date: ' . gmdate('Ymd\THis\Z', $time),
    ]);
    $type = $_POST['type'];
    $content = $_POST['content'];
    $name = !empty($_POST['name']) ? $_POST['name'] : "";
    $ttl = !empty($_POST['ttl']) ? $_POST['ttl'] : 600;

    switch ($type) {
        case 'MX':
            $payload = '{"type":"' . $type . '","name":"' . $name . '","content": "' . $content . '","ttl": ' . $ttl . ', "prio": ' . $_POST['prio'] . '}';
            break;
        case 'SRV':
            $payload = '{"type":"' . $type . '","name":"' . $name . '","content": "' . $content . '","ttl": ' . $ttl . ', "prio": ' . $_POST['prio'] . ', "port": ' . $_POST['port'] . ', "weight": ' . $_POST['weight'] . '}';
            break;
        default:
            $payload = '{"type":"' . $type . '","name":"' . $name . '","content": "' . $content . '","ttl": ' . $ttl . '}';
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    $decoded = json_decode($response);

    curl_close($ch);
    $_SESSION['status'] = $decoded->status;
    if(!empty($decoded->errors->content)){
        $_SESSION['errorContent'] = $decoded->errors->content[0];
    }else if(!empty($decoded->errors->name)){
        $_SESSION['errorContent'] = $decoded->errors->name[0];
    }else{
        $_SESSION['errorContent'] = "";
    }

    header('Location: index.php');
}

