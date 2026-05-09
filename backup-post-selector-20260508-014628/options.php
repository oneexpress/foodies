<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

function out(array $a): never {
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

$type = trim((string)($_GET['type'] ?? ''));
$target = trim((string)($_GET['target'] ?? ''));
$loc = trim((string)($_GET['loc'] ?? ''));

$nationalitiesChina = [
  ['slug'=>'china','code'=>'china','name_zh'=>'中国','name_en'=>'China'],
  ['slug'=>'malaysia','code'=>'malaysia','name_zh'=>'马来西亚','name_en'=>'Malaysia'],
];

$nationalitiesForeign = [
  ['slug'=>'foreign-worker','code'=>'foreign-worker','name_zh'=>'外籍劳工','name_en'=>'Foreign Worker'],
  ['slug'=>'indonesia','code'=>'indonesia','name_zh'=>'印尼','name_en'=>'Indonesia'],
  ['slug'=>'bangladesh','code'=>'bangladesh','name_zh'=>'孟加拉','name_en'=>'Bangladesh'],
  ['slug'=>'myanmar','code'=>'myanmar','name_zh'=>'缅甸','name_en'=>'Myanmar'],
  ['slug'=>'vietnam','code'=>'vietnam','name_zh'=>'越南','name_en'=>'Vietnam'],
  ['slug'=>'nepal','code'=>'nepal','name_zh'=>'尼泊尔','name_en'=>'Nepal'],
  ['slug'=>'pakistan','code'=>'pakistan','name_zh'=>'巴基斯坦','name_en'=>'Pakistan'],
  ['slug'=>'india','code'=>'india','name_zh'=>'印度','name_en'=>'India'],
  ['slug'=>'philippines','code'=>'philippines','name_zh'=>'菲律宾','name_en'=>'Philippines'],
];

$categories = [
  ['slug'=>'svc-visa-permit','code'=>'svc-visa-permit','name_zh'=>'签证服务','name_en'=>'VISA & PERMIT'],
  ['slug'=>'svc-marketplace','code'=>'svc-marketplace','name_zh'=>'餐车商机','name_en'=>'MARKETPLACE'],
  ['slug'=>'svc-jobs-posting','code'=>'svc-jobs-posting','name_zh'=>'招聘发布','name_en'=>'JOBS POSTING'],
  ['slug'=>'svc-transport','code'=>'svc-transport','name_zh'=>'豪华 MPV 租车','name_en'=>'LUXURY MPV RENTAL'],
  ['slug'=>'svc-accommodation','code'=>'svc-accommodation','name_zh'=>'旅游民宿','name_en'=>'PREMIUM HOMESTAY'],
  ['slug'=>'svc-agency-helpdesk','code'=>'svc-agency-helpdesk','name_zh'=>'线上贷款申请','name_en'=>'ONLINE LOAN APPLY'],
];

$locations = [
  ['slug'=>'kuala-lumpur','code'=>'kuala-lumpur','name_zh'=>'吉隆坡','name_en'=>'Kuala Lumpur'],
  ['slug'=>'selangor','code'=>'selangor','name_zh'=>'雪兰莪','name_en'=>'Selangor'],
  ['slug'=>'johor-bahru','code'=>'johor-bahru','name_zh'=>'新山','name_en'=>'Johor Bahru'],
  ['slug'=>'penang','code'=>'penang','name_zh'=>'槟城','name_en'=>'Penang'],
  ['slug'=>'ipoh','code'=>'ipoh','name_zh'=>'怡保','name_en'=>'Ipoh'],
  ['slug'=>'melaka','code'=>'melaka','name_zh'=>'马六甲','name_en'=>'Melaka'],
  ['slug'=>'kota-kinabalu','code'=>'kota-kinabalu','name_zh'=>'亚庇','name_en'=>'Kota Kinabalu'],
  ['slug'=>'kuching','code'=>'kuching','name_zh'=>'古晋','name_en'=>'Kuching'],
];

$sublocations = [
  'kuala-lumpur' => [
    ['slug'=>'bukit-bintang','code'=>'bukit-bintang','name_zh'=>'武吉免登','name_en'=>'Bukit Bintang'],
    ['slug'=>'cheras','code'=>'cheras','name_zh'=>'蕉赖','name_en'=>'Cheras'],
    ['slug'=>'setapak','code'=>'setapak','name_zh'=>'文良港','name_en'=>'Setapak'],
    ['slug'=>'kepong','code'=>'kepong','name_zh'=>'甲洞','name_en'=>'Kepong'],
    ['slug'=>'wangsa-maju','code'=>'wangsa-maju','name_zh'=>'旺沙玛朱','name_en'=>'Wangsa Maju'],
    ['slug'=>'mont-kiara','code'=>'mont-kiara','name_zh'=>'满家乐','name_en'=>'Mont Kiara'],
    ['slug'=>'bangsar','code'=>'bangsar','name_zh'=>'孟沙','name_en'=>'Bangsar'],
    ['slug'=>'klcc','code'=>'klcc','name_zh'=>'KLCC','name_en'=>'KLCC'],
    ['slug'=>'old-klang-road','code'=>'old-klang-road','name_zh'=>'旧巴生路','name_en'=>'Old Klang Road'],
    ['slug'=>'sri-petaling','code'=>'sri-petaling','name_zh'=>'大城堡','name_en'=>'Sri Petaling'],
  ],
  'selangor' => [
    ['slug'=>'petaling-jaya','code'=>'petaling-jaya','name_zh'=>'八打灵再也','name_en'=>'Petaling Jaya'],
    ['slug'=>'subang-jaya','code'=>'subang-jaya','name_zh'=>'梳邦再也','name_en'=>'Subang Jaya'],
    ['slug'=>'puchong','code'=>'puchong','name_zh'=>'蒲种','name_en'=>'Puchong'],
    ['slug'=>'shah-alam','code'=>'shah-alam','name_zh'=>'莎阿南','name_en'=>'Shah Alam'],
    ['slug'=>'kajang','code'=>'kajang','name_zh'=>'加影','name_en'=>'Kajang'],
    ['slug'=>'ampang','code'=>'ampang','name_zh'=>'安邦','name_en'=>'Ampang'],
    ['slug'=>'rawang','code'=>'rawang','name_zh'=>'万挠','name_en'=>'Rawang'],
    ['slug'=>'klang','code'=>'klang','name_zh'=>'巴生','name_en'=>'Klang'],
    ['slug'=>'seri-kembangan','code'=>'seri-kembangan','name_zh'=>'沙登','name_en'=>'Seri Kembangan'],
    ['slug'=>'cyberjaya','code'=>'cyberjaya','name_zh'=>'赛城','name_en'=>'Cyberjaya'],
  ],
  'johor-bahru' => [
    ['slug'=>'jb-city','code'=>'jb-city','name_zh'=>'新山市区','name_en'=>'JB City'],
    ['slug'=>'skudai','code'=>'skudai','name_zh'=>'士姑来','name_en'=>'Skudai'],
    ['slug'=>'mount-austin','code'=>'mount-austin','name_zh'=>'奥斯汀','name_en'=>'Mount Austin'],
    ['slug'=>'permas-jaya','code'=>'permas-jaya','name_zh'=>'百万镇','name_en'=>'Permas Jaya'],
    ['slug'=>'bukit-indah','code'=>'bukit-indah','name_zh'=>'武吉英达','name_en'=>'Bukit Indah'],
  ],
];

if ($type === 'nationality' || $type === 'nationalities') {
  out(['ok'=>true,'rows'=>($target === 'foreign' ? $nationalitiesForeign : $nationalitiesChina)]);
}
if ($type === 'category' || $type === 'categories') {
  out(['ok'=>true,'rows'=>$categories]);
}
if ($type === 'location' || $type === 'locations') {
  out(['ok'=>true,'rows'=>$locations]);
}
if ($type === 'sublocation' || $type === 'sublocations') {
  out(['ok'=>true,'rows'=>($sublocations[$loc] ?? [])]);
}

out(['ok'=>false,'error'=>'unknown_type','rows'=>[]]);
