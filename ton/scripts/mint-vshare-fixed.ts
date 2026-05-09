import { Address, beginCell, toNano } from '@ton/core';
import { NetworkProvider } from '@ton/blueprint';

export async function run(provider: NetworkProvider) {

  const admin = provider.sender().address;

  const required = Address.parse(
    'UQBdYfGArtoCUmBs5TjYQtfPFfQuGC2Ydbj2pQr3zIlNrDta'
  );

  if (!admin || admin.toString() !== required.toString()) {
    throw new Error(
      'WRONG WALLET. CONNECT TREASURY ONLY: ' + required.toString()
    );
  }

  const master = Address.parse(
    'EQBuZ2-qxsK7cMr6wmhRHhPtdt1qYiZ09JrL1RR5AQ5Gscee'
  );

  const supply = 100_000_000n * 1_000_000_000n;

  const body = beginCell()
    .storeUint(21, 32)
    .storeUint(Date.now(), 64)
    .storeAddress(required)
    .storeCoins(toNano('0.05'))
    .storeRef(
      beginCell()
        .storeUint(0x178d4519, 32)
        .storeUint(Date.now(), 64)
        .storeCoins(supply)
        .storeAddress(master)
        .storeAddress(required)
        .storeCoins(0n)
        .storeBit(false)
        .endCell()
    )
    .endCell();

  console.log('');
  console.log('==============================');
  console.log('MINTING vSHARE FIXED SUPPLY');
  console.log('MASTER:', master.toString());
  console.log('SUPPLY:', supply.toString());
  console.log('==============================');
  console.log('');

  await provider.sender().send({
    to: master,
    value: toNano('0.30'),
    body
  });

  console.log('vSHARE mint submitted');
}
