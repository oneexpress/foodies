<?php
declare(strict_types=1);
/**
 * Auto issue / update Vendor Quality Cert
 * Source signals (minimal):
 *  - approved marketplace posts (svc-marketplace)
 *  - review counts / avg rating (if you store elsewhere, join later)
 *
 * Safe defaults if review tables not ready:
 *  - approved_posts_count as proxy for activity
 */

header('Content-Type: application/json; charset=UTF-8');
function out($a){ echo json_encode($a, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }

$db = new mysqli('localhost','root','','visa_db');
if ($db->connect_errno) out(['ok'=>false,'error'=>'db_failed']);
$db->set_charset('utf8mb4');

/** --- CONFIG --- **/
$MIN_POSTS = 5;        // minimal activity
$MIN_VISITS = 10;      // minimal verified visits (proxy below if not available)
$MIN_RATING = 4.0;     // minimal avg rating

/** --- HELPERS --- **/
function slugify($s){
  $s = preg_replace('/[^a-zA-Z0-9]+/','-', strtolower($s));
  return trim($s,'-');
}
function hash4(){
  return strtoupper(substr(bin2hex(random_bytes(3)),0,4));
}
function publicCode($brand, $h){ return 'ONE-' . strtoupper(preg_replace('/[^A-Z0-9]/','', $brand)) . '-' . $h; }
function internalCode($brand, $h){ return '991-VQC-' . strtoupper(preg_replace('/[^A-Z0-9]/','', $brand)) . '-' . $h; }

/**
 * Derive vendor stats from marketplace posts.
 * Assumes:
 *  - visa_free_posts.target in ('china','foreign')
 *  - listing_type / category indicates marketplace entries (svc-marketplace)
 *  - vendor identified by title or a vendor field; here we group by title as fallback
 */
$sql = "
  SELECT
    LOWER(REPLACE(title,' ','')) AS vendor_key,
    MIN(title) AS vendor_name,
    COUNT(*) AS approved_posts,
    SUM(CASE WHEN sync_status='synced' THEN 1 ELSE 0 END) AS synced_posts
  FROM visa_free_posts
  WHERE sync_status IN ('pending','synced')
  GROUP BY vendor_key
";
$res = $db->query($sql);

$issued = 0; $updated = 0; $skipped = 0;

while ($row = $res->fetch_assoc()) {
  $vendorName = $row['vendor_name'] ?: 'Vendor';
  $vendorSlug = slugify($vendorName);

  // Proxy metrics (replace later with real review/visit tables)
  $approvedPosts = (int)$row['approved_posts'];
  $verifiedVisits = max(0, (int)$row['synced_posts'] * 3); // proxy: 3 visits per synced post
  $avgRating = 4.2; // default baseline; replace with real avg when available

  // Gate
  if ($approvedPosts < $MIN_POSTS || $verifiedVisits < $MIN_VISITS || $avgRating < $MIN_RATING) {
    $skipped++;
    continue;
  }

  // Check existing
  $q = $db->prepare("SELECT id, public_code FROM ev_vendor_quality_certs WHERE vendor_slug=? LIMIT 1");
  $q->bind_param('s', $vendorSlug);
  $q->execute();
  $ex = $q->get_result()->fetch_assoc();

  if (!$ex) {
    // issue new
    $h = hash4();
    $pub = publicCode($vendorSlug, $h);
    $int = internalCode($vendorSlug, $h);

    $ins = $db->prepare("
      INSERT INTO ev_vendor_quality_certs
      (public_code, internal_code, vendor_name, vendor_slug, mob_type, location_name,
       avg_rating, verified_visits, approved_reviews, cert_status, meta_json)
      VALUES
      (?,?,?,?,?,?,?,?,?,'active',?)
    ");
    $mobType = 'mob-foodtruck'; // default; you can map from subcategory later
    $loc = 'Malaysia';
    $approvedReviews = (int)round($verifiedVisits * 0.4);
    $meta = json_encode(['source'=>'auto-issuer','proxy'=>true], JSON_UNESCAPED_UNICODE);

    $ins->bind_param(
      'ssssssdiis',
      $pub, $int, $vendorName, $vendorSlug, $mobType, $loc,
      $avgRating, $verifiedVisits, $approvedReviews, $meta
    );
    $ins->execute();
    $issued++;
  } else {
    // update existing metrics
    $upd = $db->prepare("
      UPDATE ev_vendor_quality_certs
      SET vendor_name=?, avg_rating=?, verified_visits=?, approved_reviews=?, cert_status='active', updated_at=NOW()
      WHERE vendor_slug=?
    ");
    $approvedReviews = (int)round($verifiedVisits * 0.4);
    $upd->bind_param('sdiss', $vendorName, $avgRating, $verifiedVisits, $approvedReviews, $vendorSlug);
    $upd->execute();
    $updated++;
  }
}

out(['ok'=>true,'issued'=>$issued,'updated'=>$updated,'skipped'=>$skipped]);

