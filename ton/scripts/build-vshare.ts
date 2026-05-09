import { compile } from '@ton/blueprint';

export async function run() {
  console.log('Building vSHARE real Jetton contract...');
  const code = await compile('vSHARE');
  console.log('vSHARE build OK:', code.hash().toString('hex'));
}
