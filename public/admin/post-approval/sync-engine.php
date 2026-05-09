<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors','1');

const DB_USER='oneexpressvisa';
const DB_PASS='$Express4653';

function db($name){
  return new PDO("mysql:host=localhost;dbname={$name};charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
}

function log_error($postId,$msg){
  try{
    db('visa_ops_db')->prepare("INSERT INTO ev_sync_errors(module,post_id,error_text) VALUES('sync-v5',?,?)")->execute([$postId,$msg]);
  }catch(Throwable $e){}
}

function slug($s){
  $s=strtolower(trim($s));
  $s=preg_replace('/[^a-z0-9]+/','-',$s);
  return trim($s,'-') ?: 'expressvisa-post';
}

function sync_product($post){
  $oc=db('visa_marketplace_db');

  if(!empty($post['marketplace_product_id'])) return (int)$post['marketplace_product_id'];

  $model='EVPOST-'.$post['id'];
  $q=$oc->prepare("SELECT product_id FROM oc_product WHERE model=? LIMIT 1");
  $q->execute([$model]);
  $existing=(int)($q->fetchColumn() ?: 0);
  if($existing) return $existing;

  $img=trim((string)($post['image_path'] ?? ''));
  $img=preg_replace('#^/marketplace/image/#','',$img);
  $img=ltrim($img,'/');
  if(!$img) $img='catalog/default.png';

  $price=(float)($post['price'] ?? 0.01);

  $oc->prepare("INSERT INTO oc_product SET model=?, price=?, quantity=1, minimum=1, subtract=0, stock_status_id=7, date_available=CURDATE(), status=1, image=?, date_added=NOW(), date_modified=NOW()")
     ->execute([$model,$price,$img]);

  $pid=(int)$oc->lastInsertId();

  $desc=trim((string)($post['description'] ?? ''));
  if(!empty($post['whatsapp'])){
    $wa=preg_replace('/\D+/','',$post['whatsapp']);
    if($wa) $desc .= "\n\nWhatsApp: https://wa.me/".$wa;
  }

  $oc->prepare("INSERT INTO oc_product_description SET product_id=?, language_id=1, name=?, description=?, meta_title=?, meta_description=''")
     ->execute([$pid,$post['title'],nl2br(htmlspecialchars($desc,ENT_QUOTES,'UTF-8')),$post['title']]);

  $cat=(int)($oc->query("SELECT category_id FROM oc_category ORDER BY category_id ASC LIMIT 1")->fetchColumn() ?: 0);
  if($cat>0) $oc->prepare("INSERT IGNORE INTO oc_product_to_category SET product_id=?, category_id=?")->execute([$pid,$cat]);
  $oc->prepare("INSERT IGNORE INTO oc_product_to_store SET product_id=?, store_id=0")->execute([$pid]);

  $kw=slug($post['title'].'-'.$pid);
  $oc->prepare("INSERT IGNORE INTO oc_seo_url SET store_id=0, language_id=1, query=?, keyword=?")->execute(['product_id='.$pid,$kw]);

  return $pid;
}

function sync_flarum($dbName,$post,$pid){
  $fl=db($dbName);
  $title=(string)$post['title'];

  $q=$fl->prepare("SELECT id FROM fl_discussions WHERE title=? ORDER BY id DESC LIMIT 1");
  $q->execute([$title]);
  $existing=(int)($q->fetchColumn() ?: 0);
  if($existing) return $existing;

  $market="https://expressvisa.one/marketplace/index.php?route=product/product&product_id=".$pid;
  $wa=preg_replace('/\D+/','',(string)($post['whatsapp'] ?? ''));

  $content="🚚 {$title}\n\nMarketplace:\n{$market}\n\n";
  if($wa) $content.="WhatsApp:\nhttps://wa.me/{$wa}\n\n";
  $content.=trim((string)($post['description'] ?? ''));

  $fl->prepare("INSERT INTO fl_discussions(title,comment_count,participant_count,post_number_index,created_at,last_posted_at,user_id,first_post_id,last_post_id,is_private) VALUES(?,0,0,0,NOW(),NOW(),1,NULL,NULL,0)")
     ->execute([$title]);

  $did=(int)$fl->lastInsertId();

  $fl->prepare("INSERT INTO fl_posts(discussion_id,number,created_at,user_id,type,content,is_private) VALUES(?,1,NOW(),1,'comment',?,0)")
     ->execute([$did,$content]);

  $postId=(int)$fl->lastInsertId();

  $fl->prepare("UPDATE fl_discussions SET first_post_id=?, last_post_id=?, comment_count=1, post_number_index=1 WHERE id=?")
     ->execute([$postId,$postId,$did]);

  return $did;
}

$id=(int)($_GET['id'] ?? $_POST['id'] ?? 0);

$visa=db('visa_db');

$sql=$id>0 ? "SELECT * FROM visa_free_posts WHERE id=? LIMIT 1" : "SELECT * FROM visa_free_posts WHERE sync_status='pending' ORDER BY id ASC LIMIT 20";
$stmt=$visa->prepare($sql);
$stmt->execute($id>0 ? [$id] : []);
$rows=$id>0 ? array_filter([$stmt->fetch()]) : $stmt->fetchAll();

foreach($rows as $post){
  try{
    $postId=(int)$post['id'];
    $pid=sync_product($post);

    $foreignId=sync_flarum('flarum_db',$post,$pid);
    $chinaId=0;
    try{ $chinaId=sync_flarum('flarum_china_db',$post,$pid); }catch(Throwable $e){ log_error($postId,'China sync skipped: '.$e->getMessage()); }

    $visa->prepare("UPDATE visa_free_posts SET marketplace_product_id=?, community_discussion_id=?, sync_status='synced', updated_at=NOW() WHERE id=?")
         ->execute([$pid,$foreignId,$postId]);

    echo "OK post={$postId} product={$pid} foreign_discussion={$foreignId} china_discussion={$chinaId}<br>\n";

  }catch(Throwable $e){
    log_error((int)$post['id'],$e->getMessage());
    echo "FAILED post=".$post['id']." ".$e->getMessage()."<br>\n";
  }
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
