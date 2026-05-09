<?php
declare(strict_types=1);
header('Content-Type: text/plain; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors','0');

$mode = $argv[1] ?? ($_GET['mode'] ?? 'audit');
if (!in_array($mode, ['audit','dry-run','sync'], true)) $mode = 'audit';
$WRITE = ($mode === 'sync');

function out(string $s=''): void { echo $s . PHP_EOL; }
function rootdb(): mysqli {
  $m = new mysqli('localhost','root','');
  if ($m->connect_errno) throw new RuntimeException($m->connect_error);
  $m->set_charset('utf8mb4');
  return $m;
}
function db(string $name): mysqli {
  $m = new mysqli('localhost','root','',$name);
  if ($m->connect_errno) throw new RuntimeException("DB failed {$name}: ".$m->connect_error);
  $m->set_charset('utf8mb4');
  return $m;
}
function esc(mysqli $db, string $s): string { return $db->real_escape_string($s); }
function q(mysqli $db, string $sql): void {
  if (!$db->query($sql)) throw new RuntimeException($db->error."\nSQL=".$sql);
}
function hasTable(string $db, string $table): bool {
  $r = rootdb()->query("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema='".esc(rootdb(),$db)."' AND table_name='".esc(rootdb(),$table)."'");
  return $r && (int)$r->fetch_assoc()['c'] > 0;
}
function allDbs(): array {
  $r = rootdb()->query("SHOW DATABASES");
  $out = [];
  while ($row = $r->fetch_row()) {
    if (!preg_match('/^(information_schema|mysql|performance_schema|sys)$/', $row[0])) $out[] = $row[0];
  }
  return $out;
}
function detectFlarumDbs(): array {
  $rows = [];
  foreach (allDbs() as $name) {
    if (hasTable($name,'tags') && (hasTable($name,'discussions') || hasTable($name,'posts'))) {
      $d = db($name);
      $cnt = 0; $zh = 0;
      $r = $d->query("SELECT COUNT(*) c FROM tags");
      if ($r) $cnt = (int)$r->fetch_assoc()['c'];
      $r = $d->query("SELECT COUNT(*) c FROM tags WHERE name REGEXP '[一-龥]'");
      if ($r) $zh = (int)$r->fetch_assoc()['c'];
      $rows[] = ['db'=>$name,'tag_count'=>$cnt,'zh_count'=>$zh];
    }
  }
  return $rows;
}
function pickChinaDb(array $dbs): string {
  foreach (['flarum_cn','china_flarum_db','china_community_db','visa_china_db'] as $prefer) {
    foreach ($dbs as $d) if ($d['db'] === $prefer) return $d['db'];
  }
  foreach ($dbs as $d) if (preg_match('/china|cn/i', $d['db'])) return $d['db'];
  foreach ($dbs as $d) if ($d['zh_count'] > 0) return $d['db'];
  return '';
}
function pickForeignDbs(array $dbs, string $chinaDb): array {
  $prefer = [
    'foreign_flarum_db',
    'flarum_db',
    'visa_flarum_indonesia',
    'visa_flarum_bangladesh',
    'visa_flarum_myanmar',
    'visa_flarum_vietnam',
    'visa_flarum_nepal',
    'visa_flarum_pakistan',
    'visa_flarum_india',
    'visa_flarum_philippines',
    'visa_flarum_thailand',
    'visa_flarum_cambodia',
  ];
  $out = [];
  foreach ($prefer as $p) foreach ($dbs as $d) if ($d['db'] === $p && $p !== $chinaDb) $out[] = $p;
  foreach ($dbs as $d) {
    if ($d['db'] !== $chinaDb && preg_match('/foreign|worker|flarum|visa_flarum/i', $d['db']) && !in_array($d['db'],$out,true)) $out[] = $d['db'];
  }
  return array_values(array_unique($out));
}

$svc = [
  ['svc-visa-permit','Visa & Permit','签证准证',10],
  ['svc-marketplace','Marketplace / Foodtruck','餐车商机',20],
  ['svc-jobs-posting','Jobs Posting','招聘发布',30],
  ['svc-luxury-mpv','Luxury MPV','豪华 MPV',40],
  ['svc-homestay','Premium Homestay','高级民宿',50],
  ['svc-online-loan','Online Loan Apply','网贷申请',60],
];

$subs = [
  ['foodtruck-rental','Foodtruck Rental','餐车出租',110],
  ['foodtruck-sale','Foodtruck Sale','餐车出售',111],
  ['foodtruck-franchise','Foodtruck Franchise','餐车加盟',112],
  ['foodtruck-supplier','Foodtruck Supplier','餐车供应商',113],
  ['foodtruck-equipment','Foodtruck Equipment','餐车设备',114],
  ['foodtruck-location-rent','Foodtruck Location Rent','餐车地点出租',115],
  ['foodtruck-event-booking','Foodtruck Event Booking','餐车活动预订',116],
  ['foodtruck-renovation','Foodtruck Renovation','餐车装修',117],
  ['foodtruck-permit','Foodtruck Permit','餐车准证',118],
  ['foodtruck-loan','Foodtruck Loan','餐车贷款',119],

  ['fw-construction','Construction','建筑工',210],
  ['fw-factory','Factory','工厂工',211],
  ['fw-plantation','Plantation','种植园工',212],
  ['fw-cleaning','Cleaning','清洁工',213],
  ['fw-restaurant','Restaurant','餐饮工',214],
  ['fw-domestic-helper','Domestic Helper','家庭帮佣',215],
  ['fw-driver','Driver','司机',216],
  ['fw-security','Security','保安',217],
  ['fw-warehouse','Warehouse','仓库工',218],
  ['fw-general-labour','General Labour','普通劳工',219],
];

$locs = [
  ['loc-kuala-lumpur','Kuala Lumpur','吉隆坡',300],
  ['loc-selangor','Selangor','雪兰莪',301],
  ['loc-johor-bahru','Johor Bahru','新山',302],
  ['loc-penang','Penang','槟城',303],
  ['loc-ipoh','Ipoh','怡保',304],
  ['loc-melaka','Melaka','马六甲',305],
  ['loc-kota-kinabalu','Kota Kinabalu','亚庇',306],
  ['loc-kuching','Kuching','古晋',307],

  ['bukit-bintang','Bukit Bintang','武吉免登',410],
  ['cheras','Cheras','蕉赖',411],
  ['setapak','Setapak','文良港',412],
  ['kepong','Kepong','甲洞',413],
  ['wangsa-maju','Wangsa Maju','旺沙玛珠',414],
  ['mont-kiara','Mont Kiara','满家乐',415],
  ['bangsar','Bangsar','孟沙',416],
  ['klcc','KLCC','吉隆坡城中城',417],
  ['old-klang-road','Old Klang Road','旧巴生路',418],
  ['sri-petaling','Sri Petaling','斯里八打灵',419],

  ['petaling-jaya','Petaling Jaya','八打灵再也',510],
  ['subang-jaya','Subang Jaya','梳邦再也',511],
  ['puchong','Puchong','蒲种',512],
  ['shah-alam','Shah Alam','莎阿南',513],
  ['kajang','Kajang','加影',514],
  ['ampang','Ampang','安邦',515],
  ['rawang','Rawang','万挠',516],
  ['klang','Klang','巴生',517],
  ['seri-kembangan','Seri Kembangan','沙登岭',518],
  ['cyberjaya','Cyberjaya','赛城',519],

  ['jb-city','JB City','新山市中心',610],
  ['skudai','Skudai','士姑来',611],
  ['mount-austin','Mount Austin','茂奥斯汀',612],
  ['permas-jaya','Permas Jaya','百万镇',613],
  ['bukit-indah','Bukit Indah','武吉英达',614],
];

function syncTagSet(string $dbName, array $rows, bool $china, bool $write): array {
  $db = db($dbName);
  $stats = ['insert'=>0,'update'=>0,'same'=>0];

  foreach ($rows as [$slug,$en,$zh,$pos]) {
    $name = $china ? $zh : $en;
    $slugE = esc($db,$slug);
    $nameE = esc($db,$name);

    $r = $db->query("SELECT id,name,position,is_hidden FROM tags WHERE slug='{$slugE}' LIMIT 1");
    if ($r && $r->num_rows) {
      $old = $r->fetch_assoc();
      $needs = ($old['name'] !== $name || (int)$old['position'] !== (int)$pos || (int)$old['is_hidden'] !== 0);
      if ($needs) {
        $stats['update']++;
        out(($write?'UPDATE':'WOULD UPDATE')." {$dbName}.tags slug={$slug} name={$old['name']} -> {$name}");
        if ($write) q($db, "UPDATE tags SET name='{$nameE}', position={$pos}, is_hidden=0 WHERE id=".(int)$old['id']);
      } else {
        $stats['same']++;
      }
    } else {
      $stats['insert']++;
      out(($write?'INSERT':'WOULD INSERT')." {$dbName}.tags slug={$slug} name={$name}");
      if ($write) {
        q($db, "INSERT INTO tags
          (name,slug,description,color,background_path,background_mode,icon,parent_id,position,is_restricted,is_hidden,discussion_count,last_posted_at,last_posted_discussion_id,last_posted_user_id)
          VALUES
          ('{$nameE}','{$slugE}','991 locked taxonomy','','','','',NULL,{$pos},0,0,0,NULL,NULL,NULL)");
      }
    }
  }
  return $stats;
}

out("991 Sync Engine");
out("mode={$mode}");
out("write=".($WRITE?'YES':'NO'));
out("");

$flarum = detectFlarumDbs();
out("[Detected Flarum DBs]");
foreach ($flarum as $d) out($d['db']." tags=".$d['tag_count']." zh=".$d['zh_count']);

$chinaDb = pickChinaDb($flarum);
$foreignDbs = pickForeignDbs($flarum, $chinaDb);

out("");
out("CHINA_DB={$chinaDb}");
out("FOREIGN_DBS=".implode(',', $foreignDbs));

if (!$chinaDb) {
  out("ERROR: China Flarum DB not detected.");
  exit(2);
}
if (!$foreignDbs) {
  out("ERROR: Foreign Flarum DBs not detected.");
  exit(2);
}

$all = array_merge($svc,$subs,$locs);

out("");
out("[China target]");
$stats = syncTagSet($chinaDb, $all, true, $WRITE);
out("china stats=".json_encode($stats, JSON_UNESCAPED_UNICODE));

out("");
out("[Foreign targets]");
foreach ($foreignDbs as $fdb) {
  out("### {$fdb}");
  $stats = syncTagSet($fdb, $all, false, $WRITE);
  out("foreign stats {$fdb}=".json_encode($stats, JSON_UNESCAPED_UNICODE));
}

out("");
out("DONE");
