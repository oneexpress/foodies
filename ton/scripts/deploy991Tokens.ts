import { beginCell, toNano } from '@ton/core';
import { compile, NetworkProvider } from '@ton/blueprint';
import { JettonMinter } from '../wrappers/JettonMinter';
import * as fs from 'fs';

function offchain(uri: string) {
  return beginCell().storeUint(1, 8).storeStringTail(uri).endCell();
}

async function deployOne(provider: NetworkProvider, envKey: string, metaUrl: string) {
  const admin = provider.sender().address;
  if (!admin) throw new Error('No wallet connected. Blueprint TonConnect approval required.');

  const minterCode = await compile('JettonMinter');
  const walletCode = await compile('JettonWallet');

  const jetton = JettonMinter.createFromConfig({
    admin,
    content: offchain(metaUrl),
    walletCode
  }, minterCode);

  console.log('');
  console.log('DEPLOYING', envKey);
  console.log('MASTER', jetton.address.toString());
  console.log('METADATA', metaUrl);

  await provider.open(jetton).sendDeploy(provider.sender(), toNano('0.20'));

  fs.appendFileSync('artifacts/deployed-tokens.env', `${envKey}=${jetton.address.toString()}\n`);
}

export async function run(provider: NetworkProvider) {
  fs.mkdirSync('artifacts', { recursive: true });
  fs.writeFileSync('artifacts/deployed-tokens.env', '');

  await deployOne(provider, 'VUSDT_JETTON_MASTER', 'https://expressvisa.one/metadata/vusdt.json');
  await deployOne(provider, 'VSHARE_JETTON_MASTER', 'https://expressvisa.one/metadata/vshare.json');

  console.log('');
  console.log('DONE. Saved artifacts/deployed-tokens.env');
}
