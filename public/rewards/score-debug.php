<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ExpressVisa 991 — Score Debugger
| Path: /var/www/html/visa/public/rewards/score-debug.php
| Version: SCORE-DEBUG-v1.0.20260507
|--------------------------------------------------------------------------
| Purpose:
| - Debug Score vs vSHARE reward calculation
| - Verify live JS/CSS values
| - Inspect reward rate logic
| - Inspect DB reward/score fields
| - Detect wrong sync (score = vSHARE)
|--------------------------------------------------------------------------
*/

header('Content-Type: text/html; charset=utf-8');

date_default_timezone_set('UTC');

$ROOT = '/var/www/html/visa/public';
$REW  = $ROOT . '/rewards';

echo '<!doctype html><html><head><meta charset="utf-8">';
echo '<title>991 Score Debug</title>';

echo <<<HTML
<style>
body{
    background:#0b1020;
    color:#e5e7eb;
    font-family:Consolas,monospace;
    padding:20px;
}
h1,h2{
    color:#f59e0b;
}
pre{
    background:#111827;
    padding:12px;
    border-radius:10px;
    overflow:auto;
    border:1px solid #374151;
}
.ok{color:#22c55e;}
.bad{color:#ef4444;}
.warn{color:#f59e0b;}
table{
    border-collapse:collapse;
    width:100%;
    margin-top:10px;
}
td,th{
    border:1px solid #374151;
    padding:8px;
}
th{
    background:#1f2937;
}
</style>
HTML;

echo '</head><body>';

echo '<h1>991 Rewards Score Debugger</h1>';

function rr(string $title, callable $fn): void {
    echo "<h2>{$title}</h2>";
    echo "<pre>";
    try {
        $fn();
    } catch(Throwable $e){
        echo "ERROR: " . $e->getMessage();
    }
    echo "</pre>";
}

rr('Environment', function () {
    echo "PHP_VERSION: " . PHP_VERSION . PHP_EOL;
    echo "TIME: " . date('c') . PHP_EOL;
    echo "REWARDS_PATH: /var/www/html/visa/public/rewards" . PHP_EOL;
});

rr('Rewards Files', function () use ($REW) {

    $files = [
        "$REW/index.php",
        "$REW/assets/rewards.js",
        "$REW/assets/rewards.css",
    ];

    foreach($files as $f){
        if(file_exists($f)){
            echo "[OK] $f (" . filesize($f) . " bytes)" . PHP_EOL;
        } else {
            echo "[MISSING] $f" . PHP_EOL;
        }
    }
});

rr('Search Wrong Score Logic', function () use ($REW) {

    $patterns = [
        'score',
        'vshare',
        '0.003',
        'reward',
        'toFixed',
        'textContent',
        'innerText',
        'scoreRate',
        'score_rate',
        'dailyScore',
    ];

    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($REW)
    );

    foreach ($rii as $file) {

        if ($file->isDir()) continue;

        $path = $file->getPathname();

        if (!preg_match('/\.(php|js)$/', $path)) continue;

        $content = @file_get_contents($path);
        if (!$content) continue;

        foreach ($patterns as $p) {

            if (stripos($content, $p) !== false) {

                echo PHP_EOL;
                echo "FILE: {$path}" . PHP_EOL;
                echo "MATCH: {$p}" . PHP_EOL;

                $lines = explode("\n", $content);

                foreach ($lines as $ln => $txt) {

                    if (stripos($txt, $p) !== false) {
                        echo str_pad((string)($ln+1), 5, ' ', STR_PAD_LEFT)
                            . ' | '
                            . trim($txt)
                            . PHP_EOL;
                    }
                }
            }
        }
    }
});

rr('Current rewards.js Preview', function () use ($REW) {

    $f = "$REW/assets/rewards.js";

    if(!file_exists($f)){
        echo "missing";
        return;
    }

    $c = file_get_contents($f);

    $lines = explode("\n", $c);

    foreach($lines as $i => $line){

        $show = false;

        foreach(['score','vshare','reward','0.003'] as $k){
            if(stripos($line,$k)!==false){
                $show = true;
            }
        }

        if($show){
            echo str_pad((string)($i+1),5,' ',STR_PAD_LEFT)
                . ' | '
                . trim($line)
                . PHP_EOL;
        }
    }
});

rr('Database Check', function () {

    $env = '/var/www/secure/.env';

    $cfg = [];

    if(file_exists($env)){

        foreach(file($env) as $line){

            $line = trim($line);

            if($line==='' || str_starts_with($line,'#')) continue;

            if(!str_contains($line,'=')) continue;

            [$k,$v] = explode('=',$line,2);

            $cfg[trim($k)] = trim($v);
        }
    }

    $dbHost = $cfg['DB_HOST'] ?? 'localhost';
    $dbName = $cfg['DB_DATABASE'] ?? 'visa_db';
    $dbUser = $cfg['DB_USERNAME'] ?? 'root';
    $dbPass = $cfg['DB_PASSWORD'] ?? '';

    try{

        $pdo = new PDO(
            "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
            $dbUser,
            $dbPass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );

        echo "[OK] Connected DB: {$dbName}" . PHP_EOL . PHP_EOL;

        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        foreach($tables as $t){

            if(
                stripos($t,'reward')!==false ||
                stripos($t,'wallet')!==false ||
                stripos($t,'score')!==false ||
                stripos($t,'dig')!==false
            ){

                echo "TABLE: {$t}" . PHP_EOL;

                $cols = $pdo->query("DESCRIBE `$t`")->fetchAll(PDO::FETCH_ASSOC);

                foreach($cols as $c){

                    echo " - {$c['Field']} ({$c['Type']})" . PHP_EOL;
                }

                echo PHP_EOL;
            }
        }

    } catch(Throwable $e){

        echo "[DB ERROR] " . $e->getMessage();
    }
});

rr('Expected Locked Logic', function () {

    echo "LOCKED RULES" . PHP_EOL;
    echo "----------------------------------" . PHP_EOL;
    echo "Base vSHARE reward = 0.003 every 10s" . PHP_EOL;
    echo "1 vUSDT boost = +0.003 multiplier" . PHP_EOL;
    echo "Daily cap = 10 vSHARE" . PHP_EOL;
    echo "Foodies NFT weight affects future distribution" . PHP_EOL;
    echo "Score MUST be independent from displayed vSHARE balance" . PHP_EOL;
    echo "Current requested rule:" . PHP_EOL;
    echo "- each reward tick = +1 score" . PHP_EOL;
});

echo '<h2>Quick Fix Recommendation</h2>';

echo <<<HTML
<pre>
If score currently equals vSHARE:

BAD:
score += reward;

GOOD:
score += 1;

OR

totalScore = totalTicks;

instead of:

totalScore = totalVshare;
</pre>
HTML;

echo '
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>';
