require('dotenv').config();

console.log('====================================');
console.log(' EXPRESSVISA 991 TON PREFLIGHT ');
console.log('====================================');

const required = [
  'VUSDT_NAME',
  'VUSDT_SYMBOL',
  'VUSDT_SUPPLY',
  'VSHARE_NAME',
  'VSHARE_SYMBOL',
  'VSHARE_SUPPLY',
  'TREASURY_WALLET'
];

let fail = false;

for (const k of required) {
  if (!process.env[k]) {
    console.log('[FAIL]', k);
    fail = true;
  } else {
    console.log('[OK]', k, '=', process.env[k]);
  }
}

console.log('------------------------------------');

console.log('vUSDT');
console.log('Name   :', process.env.VUSDT_NAME);
console.log('Symbol :', process.env.VUSDT_SYMBOL);
console.log('Supply :', process.env.VUSDT_SUPPLY);

console.log('------------------------------------');

console.log('vSHARE');
console.log('Name   :', process.env.VSHARE_NAME);
console.log('Symbol :', process.env.VSHARE_SYMBOL);
console.log('Supply :', process.env.VSHARE_SUPPLY);

console.log('------------------------------------');

console.log('Treasury:', process.env.TREASURY_WALLET);

console.log('------------------------------------');

if (fail) {
  console.log('PREFLIGHT FAILED');
  process.exit(1);
}

console.log('PREFLIGHT OK');
