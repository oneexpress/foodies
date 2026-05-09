import { Address, beginCell, Cell, contractAddress, Contract, ContractProvider, Sender, SendMode } from '@ton/core';

export type JettonConfig = {
  admin: Address;
  content: Cell;
  wallet_code: Cell;
};

export function jettonConfigToCell(config: JettonConfig): Cell {
  return beginCell()
    .storeCoins(0)
    .storeAddress(config.admin)
    .storeRef(config.content)
    .storeRef(config.wallet_code)
    .endCell();
}

export class JettonDeploy implements Contract {
  constructor(readonly address: Address, readonly init?: { code: Cell; data: Cell }) {}

  static createFromConfig(config: JettonConfig, code: Cell, workchain = 0) {
    const data = jettonConfigToCell(config);
    const init = { code, data };
    return new JettonDeploy(contractAddress(workchain, init), init);
  }

  async sendDeploy(provider: ContractProvider, via: Sender, value: bigint) {
    await provider.internal(via, {
      value,
      sendMode: SendMode.PAY_GAS_SEPARATELY,
      body: beginCell().endCell()
    });
  }
}
