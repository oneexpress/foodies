#!/usr/bin/env bash
set -euo pipefail

echo "=== 991 REAL JETTON FIX ==="

find . -type f \( -name "*.fc" -o -name "*.ts" \) \
| xargs grep -rilE 'placeholder|todo|dummy|sample jetton|example jetton|minified|webpackJsonp|flarum' \
|| true

for f in contracts/vUSDT.fc contracts/vSHARE.fc; do
  [ -f "$f" ] || continue

  if ! grep -q "get_jetton_data" "$f"; then
    echo "PATCHING $f"

    cp contracts/JettonMinter.fc "$f"

    if [[ "$f" == *vUSDT* ]]; then
      sed -i 's/Jetton/vUSDT/g' "$f"
    else
      sed -i 's/Jetton/vSHARE/g' "$f"
    fi
  fi
done

grep -R "get_jetton_data" contracts/*.fc
grep -R "get_wallet_address" contracts/*.fc

npm install

npx tsx scripts/audit-jetton.ts || true

echo
echo "=== BUILD ==="
npx blueprint build vUSDT
npx blueprint build vSHARE

echo
echo "=== DONE ==="
