<?php
function toncenter_balance($address) {
    $url = "https://toncenter.com/api/v2/getAddressBalance?address=" . urlencode($address);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    if (!$res) return 0;

    $json = json_decode($res, true);
    if (!isset($json['ok']) || !$json['ok']) return 0;

    // nanotons → TON
    return $json['result'] / 1000000000;
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
