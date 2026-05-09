import fs from 'fs';
import path from 'path';

const bad = /(mnemonic|seed phrase|private key|secretKey|WALLET_MNEMONIC)/i;
const roots = ['scripts','contracts','wrappers'];

let fail = false;

function walk(d:string){
  if(!fs.existsSync(d)) return;
  for(const f of fs.readdirSync(d)){
    const p = path.join(d,f);
    const s = fs.statSync(p);
    if(s.isDirectory()) walk(p);
    else{
      const c = fs.readFileSync(p,'utf8');
      if(bad.test(c)){
        console.error('FORBIDDEN SECRET REF:',p);
        fail = true;
      }
    }
  }
}

roots.forEach(walk);

if(fail) process.exit(1);

console.log('PASS GUARD');
