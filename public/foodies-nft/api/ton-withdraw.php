<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

/**
 * Foodies NFT TON treasury sweep / withdrawal helper.
 * Safe staging only:
 * - no private key
 * - no server-side signing
 * - wallet confirms with TON Connect
 * - amounts use nanoTON decimal strings
 * - avoids BitBuilder overflow by keeping payload text tiny
 */

const FOODIES_TREASURY_WALLET = 'UQBdYfGArtoCUmBs5TjYQtfPFfQuGC2Ydbj2pQr3zIlNrDta';
const FOODIES_COLLECTION_OWNER = 'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb';
const FOODIES_TREASURY_CONTRIBUTION_TON = '0.50';

function out(array $a): void {
    echo json_encode($a, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function ton_to_nanoton(string $ton): string {
    $ton = trim($ton);
    if (!preg_match('/^\d+(\.\d{1,9})?$/', $ton)) {
        throw new RuntimeException('invalid_ton_amount');
    }
    [$whole, $frac] = array_pad(explode('.', $ton, 2), 2, '');
    $frac = str_pad(substr($frac, 0, 9), 9, '0');
    $units = ltrim($whole . $frac, '0');
    return $units === '' ? '0' : $units;
}

$action = trim((string)($_POST['action'] ?? $_GET['action'] ?? 'mint_treasury_contribution'));
$wallet = trim((string)($_POST['wallet'] ?? $_GET['wallet'] ?? ''));
$certUid = trim((string)($_POST['uid'] ?? $_GET['uid'] ?? 'FOODIES-RWA-PENDING'));
$amountTon = trim((string)($_POST['amount_ton'] ?? $_GET['amount_ton'] ?? FOODIES_TREASURY_CONTRIBUTION_TON));

try {
    $amountNano = ton_to_nanoton($amountTon);
} catch (Throwable $e) {
    out(['ok'=>false, 'error'=>$e->getMessage()]);
}

if ($wallet === '') {
    out(['ok'=>false, 'error'=>'wallet_required']);
}

$comment = match ($action) {
    'withdraw_owner' => 'FOODIES_OWNER_WITHDRAW',
    'sweep_treasury' => 'FOODIES_TREASURY_SWEEP',
    default => 'FOODIES_MINT_TREASURY:' . $certUid,
};

/**
 * TON Connect transaction format.
 * Keep payload/comment short. Do not pack large JSON into cell.
 */
$tonConnectTx = [
    'validUntil' => time() + 600,
    'messages' => [[
        'address' => FOODIES_TREASURY_WALLET,
        'amount' => $amountNano,
        'payload_comment' => $comment,
    ]],
];

out([
    'ok' => true,
    'action' => $action,
    'cert_uid' => $certUid,
    'payer_wallet' => $wallet,
    'treasury_wallet' => FOODIES_TREASURY_WALLET,
    'collection_owner_wallet' => FOODIES_COLLECTION_OWNER,
    'amount_ton' => $amountTon,
    'amount_nanoton' => $amountNano,
    'gas_fee_paid_by' => 'minter_or_connected_wallet',
    'bit_overload_guard' => [
        'rule_1' => 'Do not put metadata JSON, image URL arrays, or long text inside mint payload cell.',
        'rule_2' => 'Use metadata_url only for NFT content.',
        'rule_3' => 'Use uint64/Coins for TON amounts; never oversized uint.',
        'rule_4' => 'Use short text comment only, ideally under 120 bytes.',
        'rule_5' => 'One treasury transfer message + one mint message is safer than one overloaded custom payload.',
    ],
    'ton_connect_transaction' => $tonConnectTx,
]);
