#!/usr/bin/env bash
set -euo pipefail

echo "=== FIX 991 TON ENV ==="

mkdir -p contracts wrappers scripts build

# Node 20 only
if ! node -v | grep -q '^v20'; then
  echo "Installing Node 20..."
  curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
  apt-get install -y nodejs
fi

# Clean broken deps
rm -rf node_modules package-lock.json .blueprint

# Install stable deps
npm install --save-dev \
typescript tsx @ton/core @ton/crypto @ton/blueprint \
@ton/ton ton-core ton-crypto

# Fix tsconfig
cat > tsconfig.json <<'JSON'
{
  "compilerOptions": {
    "target": "ES2022",
    "module": "CommonJS",
    "moduleResolution": "Node",
    "strict": false,
    "esModuleInterop": true,
    "resolveJsonModule": true,
    "skipLibCheck": true,
    "outDir": "dist"
  },
  "include": ["wrappers/**/*.ts","scripts/**/*.ts"]
}
JSON

# Fix blueprint config
cat > blueprint.config.ts <<'TS'
import { Config } from '@ton/blueprint';

export const config: Config = {
  network: 'mainnet'
};
TS

# Audit contracts
echo
echo "=== CONTRACT AUDIT ==="
grep -R "get_jetton_data" contracts || true
grep -R "get_wallet_address" contracts || true

# Build
echo
echo "=== BUILD ==="
npx blueprint build || true

echo
echo "=== DONE ==="
node -v
npm -v
