<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

function out(array $a): void {
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

$type = strtolower(trim($_GET['type'] ?? ''));
$target = strtolower(trim($_GET['target'] ?? ''));
$cat = strtolower(trim($_GET['cat'] ?? $_GET['category'] ?? ''));
$loc = strtolower(trim($_GET['loc'] ?? $_GET['location'] ?? ''));

$nationalitiesChina = [
  ['code'=>'china','name'=>'中国 / China','name_zh'=>'中国','name_en'=>'China'],
  ['code'=>'malaysia','name'=>'马来西亚 / Malaysia','name_zh'=>'马来西亚','name_en'=>'Malaysia'],
];

$nationalitiesForeign = [
  ['code'=>'foreign-worker','name'=>'Foreign Worker','name_zh'=>'外籍员工','name_en'=>'Foreign Worker'],
  ['code'=>'indonesia','name'=>'Indonesia','name_zh'=>'印尼','name_en'=>'Indonesia'],
  ['code'=>'bangladesh','name'=>'Bangladesh','name_zh'=>'孟加拉','name_en'=>'Bangladesh'],
  ['code'=>'myanmar','name'=>'Myanmar','name_zh'=>'缅甸','name_en'=>'Myanmar'],
  ['code'=>'vietnam','name'=>'Vietnam','name_zh'=>'越南','name_en'=>'Vietnam'],
  ['code'=>'nepal','name'=>'Nepal','name_zh'=>'尼泊尔','name_en'=>'Nepal'],
  ['code'=>'pakistan','name'=>'Pakistan','name_zh'=>'巴基斯坦','name_en'=>'Pakistan'],
  ['code'=>'india','name'=>'India','name_zh'=>'印度','name_en'=>'India'],
  ['code'=>'philippines','name'=>'Philippines','name_zh'=>'菲律宾','name_en'=>'Philippines'],
];

$categories = [
  ['code'=>'svc-marketplace','name'=>'Marketplace / 餐车商机','name_zh'=>'餐车商机','name_en'=>'Marketplace'],
  ['code'=>'svc-jobs-posting','name'=>'Jobs Posting / 外劳招聘','name_zh'=>'外劳招聘','name_en'=>'Jobs Posting'],
  ['code'=>'svc-visa-permit','name'=>'Visa & Permit / 签证准证','name_zh'=>'签证准证','name_en'=>'Visa & Permit'],
  ['code'=>'svc-luxury-mpv','name'=>'Luxury MPV / 豪华 MPV','name_zh'=>'豪华 MPV','name_en'=>'Luxury MPV'],
  ['code'=>'svc-homestay','name'=>'Homestay / 民宿','name_zh'=>'民宿','name_en'=>'Homestay'],
  ['code'=>'svc-online-loan','name'=>'Online Loan / 网贷','name_zh'=>'网贷','name_en'=>'Online Loan'],
];

$subcategories = [
  'svc-marketplace' => [
    ['code'=>'foodtruck-rental','name'=>'Foodtruck Rental'],
    ['code'=>'foodtruck-sale','name'=>'Foodtruck Sale'],
    ['code'=>'foodtruck-franchise','name'=>'Foodtruck Franchise'],
    ['code'=>'foodtruck-supplier','name'=>'Foodtruck Supplier'],
    ['code'=>'foodtruck-equipment','name'=>'Foodtruck Equipment'],
    ['code'=>'foodtruck-location-rent','name'=>'Foodtruck Location Rent'],
    ['code'=>'foodtruck-event-booking','name'=>'Foodtruck Event Booking'],
    ['code'=>'foodtruck-renovation','name'=>'Foodtruck Renovation'],
    ['code'=>'foodtruck-permit','name'=>'Foodtruck Permit'],
    ['code'=>'foodtruck-loan','name'=>'Foodtruck Loan'],
  ],
  'svc-jobs-posting' => [
    ['code'=>'fw-construction','name'=>'Construction'],
    ['code'=>'fw-factory','name'=>'Factory'],
    ['code'=>'fw-plantation','name'=>'Plantation'],
    ['code'=>'fw-cleaning','name'=>'Cleaning'],
    ['code'=>'fw-restaurant','name'=>'Restaurant'],
    ['code'=>'fw-domestic-helper','name'=>'Domestic Helper'],
    ['code'=>'fw-driver','name'=>'Driver'],
    ['code'=>'fw-security','name'=>'Security'],
    ['code'=>'fw-warehouse','name'=>'Warehouse'],
    ['code'=>'fw-general-labour','name'=>'General Labour'],
  ],
];

$locations = [
  ['code'=>'kuala-lumpur','name'=>'Kuala Lumpur'],
  ['code'=>'selangor','name'=>'Selangor'],
  ['code'=>'johor-bahru','name'=>'Johor Bahru'],
  ['code'=>'penang','name'=>'Penang'],
  ['code'=>'ipoh','name'=>'Ipoh'],
  ['code'=>'melaka','name'=>'Melaka'],
  ['code'=>'kota-kinabalu','name'=>'Kota Kinabalu'],
  ['code'=>'kuching','name'=>'Kuching'],
];

$sublocations = [
  'kuala-lumpur' => [
    ['code'=>'bukit-bintang','name'=>'Bukit Bintang'],
    ['code'=>'cheras','name'=>'Cheras'],
    ['code'=>'setapak','name'=>'Setapak'],
    ['code'=>'kepong','name'=>'Kepong'],
    ['code'=>'wangsa-maju','name'=>'Wangsa Maju'],
    ['code'=>'mont-kiara','name'=>'Mont Kiara'],
    ['code'=>'bangsar','name'=>'Bangsar'],
    ['code'=>'klcc','name'=>'KLCC'],
    ['code'=>'old-klang-road','name'=>'Old Klang Road'],
    ['code'=>'sri-petaling','name'=>'Sri Petaling'],
  ],
  'selangor' => [
    ['code'=>'petaling-jaya','name'=>'Petaling Jaya'],
    ['code'=>'subang-jaya','name'=>'Subang Jaya'],
    ['code'=>'puchong','name'=>'Puchong'],
    ['code'=>'shah-alam','name'=>'Shah Alam'],
    ['code'=>'kajang','name'=>'Kajang'],
    ['code'=>'ampang','name'=>'Ampang'],
    ['code'=>'rawang','name'=>'Rawang'],
    ['code'=>'klang','name'=>'Klang'],
    ['code'=>'seri-kembangan','name'=>'Seri Kembangan'],
    ['code'=>'cyberjaya','name'=>'Cyberjaya'],
  ],
  'johor-bahru' => [
    ['code'=>'jb-city','name'=>'JB City'],
    ['code'=>'skudai','name'=>'Skudai'],
    ['code'=>'mount-austin','name'=>'Mount Austin'],
    ['code'=>'permas-jaya','name'=>'Permas Jaya'],
    ['code'=>'bukit-indah','name'=>'Bukit Indah'],
  ],
];

switch ($type) {
  case 'targets':
  case 'sync-targets':
    out(['ok'=>true,'rows'=>[
      ['code'=>'china','name'=>'China Community / 中国社区'],
      ['code'=>'foreign','name'=>'Foreign Worker Community'],
      ['code'=>'both','name'=>'Both Communities'],
    ]]);

  case 'nationalities':
  case 'audience':
  case 'audiences':
  case 'target-audience':
    if ($target === 'china') out(['ok'=>true,'rows'=>$nationalitiesChina]);
    if ($target === 'foreign') out(['ok'=>true,'rows'=>$nationalitiesForeign]);
    out(['ok'=>true,'rows'=>array_merge($nationalitiesChina, $nationalitiesForeign)]);

  case 'categories':
    out(['ok'=>true,'rows'=>$categories]);

  case 'subcategories':
    out(['ok'=>true,'rows'=>$subcategories[$cat] ?? [['code'=>'general','name'=>'General']]]);

  case 'locations':
    out(['ok'=>true,'rows'=>$locations]);

  case 'sublocations':
    out(['ok'=>true,'rows'=>$sublocations[$loc] ?? []]);

  default:
    out(['ok'=>false,'error'=>'unknown_type','type'=>$type]);
}
