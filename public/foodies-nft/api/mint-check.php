<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

const FOODIES_COLLECTION_OWNER = 'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb';
const FOODIES_VERIFY_BASE = 'https://expressvisa.one/foodies-nft/verify.php?uid=';
const FOODIES_GETGEMS_COLLECTION = 'https://getgems.io/foodies';
const FOODIES_NFT_COLLECTION_NAME = '@foodies RWA Food Reputation Collection';
const FOODIES_NFT_COLLECTION_DESCRIPTION = 'Foodies RWA Food Reputation Card collection for verified food reputation, Green & Clean Food responsibility, and platform-issued Foodies RWA certificates.';

$QR_POSITIONS = [
  1 => ['qr_x'=>82, 'qr_y'=>796, 'qr_size'=>154, 'qr_margin'=>0, 'qr_anchor'=>'top-left', 'qr_target'=>'verify_url'],
  3 => ['qr_x'=>82, 'qr_y'=>781, 'qr_size'=>167, 'qr_margin'=>0, 'qr_anchor'=>'top-left', 'qr_target'=>'verify_url'],
  5 => ['qr_x'=>78, 'qr_y'=>777, 'qr_size'=>169, 'qr_margin'=>0, 'qr_anchor'=>'top-left', 'qr_target'=>'verify_url'],
];

function out(array $a): void {
    echo json_encode($a, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function abs_url(string $path): string {
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
    return 'https://expressvisa.one' . $path;
}

$uid = trim((string)($_GET['uid'] ?? $_GET['cert_uid'] ?? $_POST['uid'] ?? $_POST['cert_uid'] ?? ''));
$stars = (int)($_GET['stars'] ?? $_POST['stars'] ?? 1);

if ($uid === '') out(['ok'=>false,'error'=>'missing_cert_uid']);
if (!in_array($stars, [1,3,5], true)) $stars = 1;

$template = match($stars) {
    5 => '/metadata/foodies/NFT/5_stars_foodies.png',
    3 => '/metadata/foodies/NFT/3_stars_foodies.png',
    default => '/metadata/foodies/NFT/1_star_foodies.png',
};

$title = match($stars) {
    5 => 'MASTER CHEF CARD',
    3 => 'PREMIUM CARD',
    default => 'RECOMMENDED CARD',
};

$score = match($stars) {
    5 => '4.0 - 5.0',
    3 => '2.0 - 3.9',
    default => '1.0 - 1.9',
};

$verifyUrl = FOODIES_VERIFY_BASE . rawurlencode($uid);
$qr = $QR_POSITIONS[$stars];

$artifactPreviewUrl = '/foodies-nft/api/nft-artifact-preview.php?uid=' . rawurlencode($uid) . '&stars=' . $stars;
$artifactFinalUrl = '/foodies-nft/api/nft-artifact-mint.php?uid=' . rawurlencode($uid) . '&stars=' . $stars;

$traits = [
    ['trait_type'=>'Project', 'value'=>'ExpressVisa Foodies NFT'],
    ['trait_type'=>'Collection', 'value'=>'@foodies RWA Food Reputation Collection'],
    ['trait_type'=>'Card Title', 'value'=>$title],
    ['trait_type'=>'Star Rating', 'value'=>(string)$stars],
    ['trait_type'=>'Average Score Range', 'value'=>$score],
    ['trait_type'=>'Unit of Responsibility', 'value'=>'Green & Clean Food'],
    ['trait_type'=>'QR Target', 'value'=>'verify_url'],
    ['trait_type'=>'Verify URL', 'value'=>$verifyUrl],
    ['trait_type'=>'Chain', 'value'=>'TON'],
    ['trait_type'=>'Owner Wallet', 'value'=>FOODIES_COLLECTION_OWNER],
];

$metadata = [
    'name' => "@foodies {$title} · {$uid}",
    'description' => FOODIES_NFT_COLLECTION_DESCRIPTION,
    'image' => abs_url($artifactFinalUrl),
    'content_url' => abs_url($artifactFinalUrl),
    'external_url' => $verifyUrl,
    'attributes' => $traits,
    'properties' => [
        'category' => 'image',
        'files' => [[
            'uri' => abs_url($artifactFinalUrl),
            'type' => 'image/png',
        ]],
        'verify_url' => $verifyUrl,
        'qr_position' => $qr,
        'template_url' => abs_url($template),
    ],
];

$collectionJson = [
    'name' => FOODIES_NFT_COLLECTION_NAME,
    'description' => FOODIES_NFT_COLLECTION_DESCRIPTION,
    'image' => 'https://expressvisa.one/metadata/foodies/foodies-collection.png',
    'cover_image' => 'https://expressvisa.one/metadata/foodies/foodies-cover.png',
    'social_links' => [
        'website' => 'https://expressvisa.one/foodies-nft/',
        'marketplace' => FOODIES_GETGEMS_COLLECTION,
    ],
    'marketplace' => 'getgems',
    'royalty' => [
        'basis_points' => 1000,
        'percent' => 10,
        'recipient' => FOODIES_COLLECTION_OWNER,
    ],
];

$getgemsPayload = [
    'marketplace' => 'getgems',
    'collection_owner' => FOODIES_COLLECTION_OWNER,
    'collection_json' => $collectionJson,
    'item' => [
        'cert_uid' => $uid,
        'star_rating' => $stars,
        'metadata_json' => $metadata,
        'metadata_url' => 'https://expressvisa.one/foodies-nft/api/nft-metadata.php?uid=' . rawurlencode($uid) . '&stars=' . $stars,
        'artifact_url' => abs_url($artifactFinalUrl),
        'verify_url' => $verifyUrl,
    ],
];

out([
    'ok' => true,
    'stage' => 'mint_precheck_ready',
    'cert_uid' => $uid,
    'stars' => $stars,
    'title' => $title,
    'verify_url' => $verifyUrl,
    'qr_position' => $qr,
    'template' => [
        'path' => $template,
        'url' => abs_url($template),
        'exists' => is_file($_SERVER['DOCUMENT_ROOT'] . $template),
    ],
    'artifact' => [
        'preview_url' => abs_url($artifactPreviewUrl),
        'mint_url' => abs_url($artifactFinalUrl),
        'qr_target' => 'verify_url',
        'status' => 'ready_for_merge_helper',
    ],
    'metadata_json' => $metadata,
    'collection_json' => $collectionJson,
    'getgems_payload' => $getgemsPayload,
    'process' => [
        ['step'=>1,'label'=>'Validate issued cert','status'=>'ready'],
        ['step'=>2,'label'=>'Merge NFT artifact with Verify URL QR','status'=>'ready'],
        ['step'=>3,'label'=>'Build metadata traits JSON','status'=>'ready'],
        ['step'=>4,'label'=>'Build Getgems collection/item payload','status'=>'ready'],
        ['step'=>5,'label'=>'Confirm TON wallet before mint','status'=>'waiting_wallet'],
        ['step'=>6,'label'=>'Submit mint transaction','status'=>'next_integration'],
    ],
]);
