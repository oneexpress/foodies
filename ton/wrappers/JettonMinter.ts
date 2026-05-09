import {
  Address,
  beginCell,
  Cell,
  contractAddress,
  Contract,
  ContractProvider,
  Sender,
  SendMode
} from '@ton/core';

export type JettonMinterConfig = {
  admin: Address;
  content: Cell;
  walletCode: Cell;
};

export function jettonMinterConfigToCell(config: JettonMinterConfig): Cell {
  return beginCell()
    .storeCoins(0)
    .storeAddress(config.admin)
    .storeRef(config.content)
    .storeRef(config.walletCode)
    .endCell();
}

export class JettonMinter implements Contract {
  constructor(
    readonly address: Address,
    readonly init?: { code: Cell; data: Cell }
  ) {}

  static createFromConfig(config: JettonMinterConfig, code: Cell, workchain = 0) {
    const data = jettonMinterConfigToCell(config);
    const init = { code, data };
    return new JettonMinter(contractAddress(workchain, init), init);
  }

  async sendDeploy(provider: ContractProvider, via: Sender, value: bigint) {
    await provider.internal(via, {
      value,
      sendMode: SendMode.PAY_GAS_SEPARATELY,
      body: beginCell().endCell()
    });
  }
}
