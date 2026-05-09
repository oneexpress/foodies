<?php
declare(strict_types=1);

/**
 * Foodies NFT artifact QR overlay config.
 * QR target = issued cert verify page link.
 */

const FOODIES_NFT_VERIFY_BASE = 'https://expressvisa.one/foodies-nft/verify.php?uid=';

function foodies_nft_qr_positions(): array {
    return [
        1 => ['qr_x'=>56, 'qr_y'=>800, 'qr_size'=>145, 'qr_margin'=>0, 'qr_anchor'=>'top-left', 'qr_target'=>'verify_url'],
        3 => ['qr_x'=>56, 'qr_y'=>800, 'qr_size'=>145, 'qr_margin'=>0, 'qr_anchor'=>'top-left', 'qr_target'=>'verify_url'],
        5 => ['qr_x'=>56, 'qr_y'=>800, 'qr_size'=>145, 'qr_margin'=>0, 'qr_anchor'=>'top-left', 'qr_target'=>'verify_url'],
    ];
}

function foodies_nft_qr_position(int $stars): array {
    $map = foodies_nft_qr_positions();
    return $map[$stars] ?? $map[1];
}

function foodies_nft_verify_url(string $certUid): string {
    return FOODIES_NFT_VERIFY_BASE . rawurlencode(trim($certUid));
}
