import fs from 'fs';
import path from 'path';

const roots = ['scripts', 'wrappers', 'contracts'];
const bad = /(mnemonic|MNEMONIC|seed phrase|privateKey|PRIVATE_KEY|secretKey|SECRET_KEY)/;

let failed = false;

function walk(dir: string) {
  if (!fs.existsSync(dir)) return;
  for (const item of fs.readdirSync(dir)) {
    const p = path.join(dir, item);
    const st = fs.statSync(p);
    if (st.isDirectory()) walk(p);
    else {
      const s = fs.readFileSync(p, 'utf8');
      if (bad.test(s)) {
        console.error('FAIL secret-like reference:', p);
        failed = true;
      }
    }
  }
}

roots.forEach(walk);

if (failed) process.exit(1);
console.log('PASS: no mnemonic/private-key references found');
