import { Address, beginCell, Contract, ContractProvider, Sender, SendMode, toNano } from '@ton/core';

export class JettonMinterMint implements Contract {
  constructor(readonly address: Address) {}

  async sendMint(
    provider: ContractProvider,
    via: Sender,
    to: Address,
    jettonAmount: bigint,
    forwardTonAmount: bigint = 1n
  ) {
    const body = beginCell()
      .storeUint(21, 32)
      .storeUint(Date.now(), 64)
      .storeAddress(to)
      .storeCoins(jettonAmount)
      .storeCoins(forwardTonAmount)
      .storeRef(beginCell().endCell())
      .endCell();

    await provider.internal(via, {
      value: toNano('0.12'),
      sendMode: SendMode.PAY_GAS_SEPARATELY,
      body
    });
  }
}
