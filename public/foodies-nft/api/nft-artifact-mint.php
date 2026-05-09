<?php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

function respond_error(string $msg, int $code = 500): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>false,'error'=>$msg], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}

$uid = trim((string)($_GET['uid'] ?? 'FOODIES-MINT-0001'));
$stars = (int)($_GET['stars'] ?? $_GET['star'] ?? 5);
if (!in_array($stars, [1,3,5], true)) $stars = 5;

$root = '/var/www/html/visa/public';
$safe = preg_replace('/[^A-Za-z0-9._-]/', '-', $uid);
$verifyUrl = 'https://expressvisa.one/foodies-nft/verify.php?uid=' . rawurlencode($uid);

$cfg = [
    1 => ['x'=>82, 'y'=>796, 'size'=>154, 'files'=>[
        "$root/metadata/foodies/templates/foodies-1star.png",
        "$root/metadata/foodies/foodies-1star.png",
    ]],
    3 => ['x'=>82, 'y'=>781, 'size'=>167, 'files'=>[
        "$root/metadata/foodies/templates/foodies-3star.png",
        "$root/metadata/foodies/foodies-3star.png",
    ]],
    5 => ['x'=>78, 'y'=>777, 'size'=>169, 'files'=>[
        "$root/metadata/foodies/templates/foodies-5star.png",
        "$root/metadata/foodies/foodies-5star.png",
    ]],
];

$template = '';
foreach ($cfg[$stars]['files'] as $f) {
    if (is_file($f) && filesize($f) > 1000) {
        $template = $f;
        break;
    }
}
if ($template === '') respond_error('template_missing_or_empty');

$outDir = "$root/metadata/foodies/generated";
if (!is_dir($outDir) && !mkdir($outDir, 0775, true)) respond_error('generated_dir_create_failed');
if (!is_writable($outDir)) respond_error('generated_dir_not_writable: '.$outDir);

$out = "$outDir/{$safe}-{$stars}star.png";

if (is_file($out) && filesize($out) > 1000 && empty($_GET['fresh'])) {
    header('Content-Type: image/png');
    readfile($out);
    exit;
}

if (!extension_loaded('gd')) respond_error('php_gd_missing');

$tmpQr = tempnam(sys_get_temp_dir(), 'foodies_qr_');
if ($tmpQr === false) respond_error('tmp_create_failed');
$tmpQr .= '.png';

$py = <<<'PY'
import sys, qrcode
url = sys.argv[1]
out = sys.argv[2]
qr = qrcode.QRCode(version=None, error_correction=qrcode.constants.ERROR_CORRECT_M, box_size=10, border=0)
qr.add_data(url)
qr.make(fit=True)
img = qr.make_image(fill_color="black", back_color="white").convert("RGB")
img.save(out)
PY;

$pyFile = tempnam(sys_get_temp_dir(), 'foodies_qr_py_') . '.py';
file_put_contents($pyFile, $py);

$cmd = 'python3 ' . escapeshellarg($pyFile) . ' ' . escapeshellarg($verifyUrl) . ' ' . escapeshellarg($tmpQr) . ' 2>&1';
exec($cmd, $lines, $rc);
@unlink($pyFile);

if ($rc !== 0 || !is_file($tmpQr) || filesize($tmpQr) < 100) {
    respond_error('qr_generate_failed: '.implode("\n", $lines));
}

$base = @imagecreatefrompng($template);
if (!$base) respond_error('template_load_failed: '.$template);

$qr = @imagecreatefrompng($tmpQr);
if (!$qr) respond_error('qr_load_failed');

$qrSize = (int)$cfg[$stars]['size'];
$x = (int)$cfg[$stars]['x'];
$y = (int)$cfg[$stars]['y'];

$canvas = imagecreatetruecolor($qrSize, $qrSize);
if (!$canvas) respond_error('qr_canvas_failed');

$white = imagecolorallocate($canvas, 255, 255, 255);
imagefill($canvas, 0, 0, $white);
imagecopyresampled($canvas, $qr, 0, 0, 0, 0, $qrSize, $qrSize, imagesx($qr), imagesy($qr));
imagecopy($base, $canvas, $x, $y, 0, 0, $qrSize, $qrSize);

imagesavealpha($base, true);
if (!imagepng($base, $out, 9)) respond_error('artifact_save_failed');

@unlink($tmpQr);
imagedestroy($base);
imagedestroy($qr);
imagedestroy($canvas);

clearstatcache(true, $out);
if (!is_file($out) || filesize($out) < 1000) respond_error('artifact_output_invalid');

header('Content-Type: image/png');
header('Cache-Control: no-store');
readfile($out);
