import 'dotenv/config';
import fs from 'fs';

const required = [
  'TON_NETWORK',
  'TON_TREASURY',
  'VUSDT_NAME',
  'VUSDT_SYMBOL',
  'VUSDT_IMAGE',
  'VUSDT_INITIAL_SUPPLY',
  'VUSDT_DECIMALS',
  'VSHARE_NAME',
  'VSHARE_SYMBOL',
  'VSHARE_IMAGE',
  'VSHARE_INITIAL_SUPPLY',
  'VSHARE_MAX_SUPPLY',
  'VSHARE_DECIMALS'
];

let ok = true;
for (const k of required) {
  if (!process.env[k]) {
    console.error(`FAIL missing env: ${k}`);
    ok = false;
  }
}

if (process.env.TON_NETWORK !== 'mainnet') {
  console.error('FAIL: TON_NETWORK must be mainnet');
  ok = false;
}

if (process.env.TON_TREASURY !== 'UQBdYfGArtoCUmBs5TjYQtfPFfQuGC2Ydbj2pQr3zIlNrDta') {
  console.error('FAIL: TON_TREASURY mismatch');
  ok = false;
}

const vusdtDecimals = Number(process.env.VUSDT_DECIMALS);
const vshareDecimals = Number(process.env.VSHARE_DECIMALS);

if (vusdtDecimals !== 6) {
  console.error('FAIL: VUSDT_DECIMALS must be 6');
  ok = false;
}
if (vshareDecimals !== 9) {
  console.error('FAIL: VSHARE_DECIMALS must be 9');
  ok = false;
}

for (const p of ['contracts', 'wrappers', 'scripts', 'build', 'artifacts', 'metadata']) {
  if (!fs.existsSync(p)) {
    console.error(`FAIL missing workspace folder: ${p}`);
    ok = false;
  }
}

console.log({
  project: 'ExpressVisa 991',
  network: process.env.TON_NETWORK,
  treasury: process.env.TON_TREASURY,
  tokens: [
    { symbol: process.env.VUSDT_SYMBOL, decimals: vusdtDecimals, initial_supply: process.env.VUSDT_INITIAL_SUPPLY, mintable: process.env.VUSDT_MINTABLE },
    { symbol: process.env.VSHARE_SYMBOL, decimals: vshareDecimals, initial_supply: process.env.VSHARE_INITIAL_SUPPLY, max_supply: process.env.VSHARE_MAX_SUPPLY, burnable: process.env.VSHARE_BURNABLE }
  ],
  deploy_mode: 'blueprint wallet flow only; no mnemonic'
});

process.exit(ok ? 0 : 1);
