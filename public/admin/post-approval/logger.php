<?php
function log_error($msg){

$pdo = new PDO("mysql:host=localhost;dbname=wems_db;charset=utf8mb4","root","");

$stmt = $pdo->prepare("
INSERT INTO poado_api_errors(module,endpoint,error_code,message,created_at)
VALUES('sync-engine','approve','SYNC_ERR',?,NOW())
");

$stmt->execute([$msg]);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
