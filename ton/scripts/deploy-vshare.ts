import { toNano, beginCell, Address } from '@ton/core';
import { compile, NetworkProvider } from '@ton/blueprint';
import { JettonMinter } from '../wrappers/JettonMinter';

export async function run(provider: NetworkProvider) {

  const walletCode = await compile('jetton-wallet');
  const minterCode = await compile('jetton-minter');

  const metadata = beginCell()
    .storeUint(1, 8)
    .storeStringTail('https://expressvisa.one/metadata/vshare.json')
    .endCell();

  const minter = JettonMinter.createFromConfig({
    admin: Address.parse('UQBdYfGArtoCUmBs5TjYQtfPFfQuGC2Ydbj2pQr3zIlNrDta'),
    content: metadata,
    walletCode: walletCode
  }, minterCode);

  console.log('');
  console.log('================================');
  console.log('DEPLOYING vSHARE');
  console.log('MASTER:', minter.address.toString());
  console.log('================================');
  console.log('');

  await provider.open(minter).sendDeploy(
    provider.sender(),
    toNano('0.25')
  );

  await provider.waitForDeploy(minter.address);

  console.log('');
  console.log('vSHARE DEPLOYED:', minter.address.toString());
}
