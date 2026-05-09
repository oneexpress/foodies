<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

$db = new mysqli('localhost','root','','visa_ops_db');
$db->set_charset('utf8mb4');

$type = $_GET['type'] ?? '';

function out($rows){
 echo json_encode(['ok'=>true,'rows'=>$rows],JSON_UNESCAPED_UNICODE);
 exit;
}

if($type==='target_audience'){
 $q=$db->query("SELECT code,name FROM visa_post_target_audience WHERE is_active=1 ORDER BY sort_order,id");
 $rows=[];
 while($r=$q->fetch_assoc()) $rows[]=$r;
 out($rows);
}

if($type==='sublocations'){
 $loc=$db->real_escape_string($_GET['loc'] ?? '');
 $q=$db->query("SELECT code,name FROM visa_post_sublocations WHERE parent_code='$loc' ORDER BY sort_order,id");
 $rows=[];
 while($r=$q->fetch_assoc()) $rows[]=$r;
 out($rows);
}

if($type==='locations'){
 out([
   ['code'=>'kuala-lumpur','name'=>'Kuala Lumpur'],
   ['code'=>'selangor','name'=>'Selangor'],
   ['code'=>'johor-bahru','name'=>'Johor Bahru'],
   ['code'=>'penang','name'=>'Penang'],
   ['code'=>'ipoh','name'=>'Ipoh']
 ]);
}

if($type==='categories'){
 out([
   ['code'=>'svc-marketplace','name'=>'Marketplace'],
   ['code'=>'svc-jobs-posting','name'=>'Jobs Posting'],
   ['code'=>'svc-visa','name'=>'Visa & Permit'],
   ['code'=>'svc-homestay','name'=>'Homestay'],
   ['code'=>'svc-mpv','name'=>'Luxury MPV']
 ]);
}

if($type==='subcategories'){

 $cat=$_GET['cat'] ?? '';
 $rows=[];

 if($cat==='svc-marketplace'){
   $rows=[
    ['code'=>'food','name'=>'Food'],
    ['code'=>'services','name'=>'Services'],
    ['code'=>'electronics','name'=>'Electronics']
   ];
 }

 if($cat==='svc-jobs-posting'){
   $rows=[
    ['code'=>'restaurant','name'=>'Restaurant'],
    ['code'=>'construction','name'=>'Construction'],
    ['code'=>'factory','name'=>'Factory']
   ];
 }

 out($rows);
}

out([]);
