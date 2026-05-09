import fs from 'fs';

const files = [
  'contracts/VUSDT.fc',
  'contracts/VSHARE.fc'
];

let fail = false;

for (const f of files) {
  if (!fs.existsSync(f)) {
    console.error(`FAIL missing ${f}`);
    fail = true;
    continue;
  }

  const s = fs.readFileSync(f, 'utf8');

  const isDummy =
    s.includes('() recv_internal() impure {}') ||
    !s.includes('get_jetton_data') ||
    !s.includes('get_wallet_address');

  if (isDummy) {
    console.error(`BLOCKED: ${f} is not a real Jetton master contract`);
    fail = true;
  }
}

if (fail) {
  console.error('');
  console.error('DO NOT DEPLOY.');
  console.error('Next required action: import real TON Jetton master + wallet contracts, then rebuild wrappers.');
  process.exit(1);
}

console.log('PASS: real Jetton contracts detected');
