<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

function out(array $a): never {
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

$target = trim((string)($_GET['target'] ?? 'china'));
$audience = trim((string)($_GET['audience'] ?? ''));
$category = trim((string)($_GET['category'] ?? ''));

$community = $target === 'foreign' ? 'https://expressvisa.one/community/' : 'https://expressvisa.one/china/community/';
$labelZh = $target === 'foreign' ? '外籍劳工' : '中国用户';
$labelEn = $target === 'foreign' ? 'Foreign Worker' : 'China Community';

out([
  'ok'=>true,
  'route'=>[
    'target'=>$target,
    'label_zh'=>$labelZh,
    'label_en'=>$labelEn,
    'community_url'=>$community,
    'audience_tag'=>$audience,
    'category_tag'=>$category,
    'marketplace_category'=>$category,
  ],
]);
