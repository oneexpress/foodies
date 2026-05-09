import fs from 'fs';

const requiredFiles = [
  'contracts/JettonMinter.fc',
  'contracts/JettonWallet.fc',
  'contracts/vUSDT.fc',
  'contracts/vSHARE.fc',
  'wrappers/JettonMinter.ts',
  'wrappers/JettonWallet.ts'
];

const requiredMinterPatterns = [
  'get_jetton_data',
  'get_wallet_address',
  'recv_internal'
];

let fail = false;

function bad(msg: string) {
  console.error('FAIL:', msg);
  fail = true;
}

for (const file of requiredFiles) {
  if (!fs.existsSync(file)) bad(`missing ${file}`);
}

for (const file of ['contracts/vUSDT.fc', 'contracts/vSHARE.fc', 'contracts/JettonMinter.fc']) {
  if (!fs.existsSync(file)) continue;

  const s = fs.readFileSync(file, 'utf8');

  if (s.includes('() recv_internal() impure {}')) {
    bad(`${file} is placeholder empty contract`);
  }

  for (const p of requiredMinterPatterns) {
    if (!s.includes(p)) bad(`${file} missing ${p}`);
  }
}

const workspaceText = requiredFiles
  .filter(fs.existsSync)
  .map(f => fs.readFileSync(f, 'utf8'))
  .join('\n');

if (/mnemonic|seed phrase|privateKey|PRIVATE_KEY|SECRET_KEY/i.test(workspaceText)) {
  bad('secret-like reference detected in contract/wrapper files');
}

if (fail) {
  console.error('\nJETTON_AUDIT=FAIL');
  process.exit(1);
}

console.log('JETTON_AUDIT=PASS');
