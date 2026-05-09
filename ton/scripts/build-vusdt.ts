import { compile } from '@ton/blueprint';

export async function run() {
  console.log('Building vUSDT real Jetton contract...');
  const code = await compile('vUSDT');
  console.log('vUSDT build OK:', code.hash().toString('hex'));
}
