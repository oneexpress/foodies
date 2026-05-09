import { Address, beginCell, toNano } from '@ton/core';
import { NetworkProvider } from '@ton/blueprint';

export async function run(provider: NetworkProvider) {
  const admin = provider.sender().address;
  const required = Address.parse('UQBdYfGArtoCUmBs5TjYQtfPFfQuGC2Ydbj2pQr3zIlNrDta');
  if (!admin || admin.toString() !== required.toString()) {
    throw new Error('WRONG WALLET. Connect treasury/admin only: ' + required.toString());
  }

  const master = Address.parse('EQD5pA15iVimLBmD-MEnfn8tGJ6RnCClUB-qdDZtRmowse0W');
  const amount = 10_000_000n * 1_000_000_000n;

  const body = beginCell()
    .storeUint(21, 32)
    .storeUint(Date.now(), 64)
    .storeAddress(required)
    .storeCoins(toNano('0.05'))
    .storeRef(
      beginCell()
        .storeUint(0x178d4519, 32)
        .storeUint(Date.now(), 64)
        .storeCoins(amount)
        .storeAddress(master)
        .storeAddress(required)
        .storeCoins(0n)
        .storeBit(false)
        .endCell()
    )
    .endCell();

  await provider.sender().send({ to: master, value: toNano('0.3'), body });
  console.log('vUSDT genesis mint sent:', amount.toString());
}
