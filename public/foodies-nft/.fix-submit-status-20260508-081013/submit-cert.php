<?php
declare(strict_types=1);

require_once __DIR__.'/_wallet.php';
if (!defined('FOODIES_COLLECTION_OWNER')) define('FOODIES_COLLECTION_OWNER', 'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb');

if (isset($pdo) && $pdo instanceof PDO) {
    $foodiesMode = strtolower(trim((string)($_POST['mode'] ?? $_GET['mode'] ?? '')));
    $foodiesCertId = (int)($_POST['cert_id'] ?? $_POST['id'] ?? $_GET['cert_id'] ?? $_GET['id'] ?? 0);
    if (in_array($foodiesMode, ['reissue','edit'], true) && $foodiesCertId > 0) {
        foodies_assert_original_issuer($pdo, $foodiesCertId, foodies_current_issuer_wallet());
    }
}


function foodies_current_issuer_wallet(): string {
    $keys = ['issuer_wallet','wallet','wallet_address','ton_wallet'];
    foreach ($keys as $k) {
        $v = trim((string)($_POST[$k] ?? $_GET[$k] ?? ''));
        if ($v !== '') return $v;
    }
    return '';
}

function foodies_assert_original_issuer(PDO $pdo, int $certId, string $issuerWallet): void {
    if ($certId <= 0 || $issuerWallet === '') {
        http_response_code(403);
        echo json_encode(['ok'=>false,'error'=>'issuer_wallet_required'], JSON_UNESCAPED_SLASHES);
        exit;
    }
    $tables = ['foodies_nft_certs','foodies_certs','foodies_certificates'];
    foreach ($tables as $t) {
        try {
            $chk = $pdo->query("SHOW TABLES LIKE ".$pdo->quote($t))->fetchColumn();
            if (!$chk) continue;
            $st = $pdo->prepare("SELECT issuer_wallet FROM `$t` WHERE id=? LIMIT 1");
            $st->execute([$certId]);
            $owner = (string)$st->fetchColumn();
            if ($owner === '' || strcasecmp($owner, $issuerWallet) !== 0) {
                http_response_code(403);
                echo json_encode(['ok'=>false,'error'=>'original_issuer_only'], JSON_UNESCAPED_SLASHES);
                exit;
            }
            return;
        } catch (Throwable $e) {}
    }
}

header('Content-Type: application/json; charset=utf-8');

function out(array $a): void { echo json_encode($a, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function envv($k,$d=''){static $c=null;if($c===null){$c=[];$f='/var/www/secure/.env';if(is_file($f)){foreach(file($f,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $l){$l=trim($l);if($l===''||str_starts_with($l,'#')||!str_contains($l,'='))continue;[$a,$b]=explode('=',$l,2);$c[trim($a)]=trim(trim($b),'"\'');}}}return $c[$k]??getenv($k)?:$d;}
function db(): PDO {
  $h=envv('VISA_DB_HOST',envv('DB_HOST','127.0.0.1')); if($h==='127.0.0.1')$h='127.0.0.1';
  $n=envv('VISA_DB_NAME','visa_db'); $u=envv('VISA_DB_USER',envv('DB_USER','root')); $p=envv('VISA_DB_PASS',envv('DB_PASS',''));
  return new PDO("mysql:host=$h;dbname=$n;charset=utf8mb4",$u,$p,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
}
function save_upload(string $key,string $uid,string $dir): string {
  if(empty($_FILES[$key]['tmp_name'])) return '';
  if(!is_uploaded_file($_FILES[$key]['tmp_name'])) return '';
  $ext=strtolower(pathinfo($_FILES[$key]['name']??'',PATHINFO_EXTENSION)); if(!in_array($ext,['jpg','jpeg','png','webp'],true))$ext='jpg';
  $base="/var/www/html/visa/public/foodies-nft/uploads/$dir/$uid.$ext";
  move_uploaded_file($_FILES[$key]['tmp_name'],$base);
  return "/foodies-nft/uploads/$dir/$uid.$ext";
}

try{
  /* wallet_required_before_issue */
  if ($wallet === '') {
    out(['ok'=>false,'error'=>'owner_wallet_required']);
  }

  $chef=trim($_POST['chef_name']??'');
  $brand=trim($_POST['brand_name']??'');
  $food=trim($_POST['food_title']??'');
  $loc=trim($_POST['location_name']??'');
  if($chef===''||$brand===''||$food===''||$loc==='') out(['ok'=>false,'error'=>'Missing required fields']);

  $star=(int)($_POST['star_class']??1); if(!in_array($star,[1,3,5],true))$star=1;
  $uid='FD'.$star.'-'.date('Ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)),0,6));
  $verify='https://expressvisa.one/foodies-rwa/verify.php?uid='.$uid;

  $foodImg=save_upload('food_image',$uid,'food');
  $chefImg=save_upload('chef_image',$uid,'chef');
  $brandImg=save_upload('branding_image',$uid,'brand');

  $map=trim($_POST['google_maps_url']??'');
  if($map==='') $map='https://www.google.com/maps/search/?api=1&query='.rawurlencode($brand.' '.$loc.' '.($_POST['address_text']??''));

  $pdo=db();
  $stmt=$pdo->prepare("INSERT INTO foodies_rwa_certs
  (cert_uid,chef_name,brand_name,food_title,star_class,reputation_tier,average_score,green_clean_food,location_name,address_text,google_maps_url,food_image_path,chef_image_path,branding_image_path,verify_url,status,responsibility_json)
  VALUES
  (:uid,:chef,:brand,:food,:star,:tier,:avg,:gc,:loc,:addr,:map,:foodimg,:chefimg,:brandimg,:verify,'pending',:resp)");
  $tier=$star===5?'MASTER_CHEF':($star===3?'PREMIUM':'RECOMMENDED');
  $stmt->execute([
    'uid'=>$uid,'chef'=>$chef,'brand'=>$brand,'food'=>$food,'star'=>$star,'tier'=>$tier,
    'avg'=>(float)($_POST['average_score']??0),'gc'=>(int)($_POST['green_clean_food']??1),
    'loc'=>$loc,'addr'=>trim($_POST['address_text']??''),'map'=>$map,
    'foodimg'=>$foodImg,'chefimg'=>$chefImg,'brandimg'=>$brandImg,'verify'=>$verify,
  ]);

  out(['ok'=>true,'cert_uid'=>$uid,'verify_url'=>$verify]);
}catch(Throwable $e){
  error_log('[foodies submit cert] '.$e->getMessage());
  out(['ok'=>false,'error'=>'submit_failed']);
}
