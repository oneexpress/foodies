import {
  Address,
  beginCell,
  Cell,
  Contract,
  ContractProvider,
  Sender,
  SendMode,
  contractAddress,
  toNano
} from '@ton/core';

export type FoodiesCollectionConfig = {
  owner: Address;
  treasury: Address;
  collectionContentUrl: string;
  royaltyFactor: number;
  royaltyBase: number;
  nextItemIndex?: bigint;
};

export function contentCell(url: string): Cell {
  return beginCell()
    .storeUint(1, 8)
    .storeStringTail(url)
    .endCell();
}

export function royaltyCell(config: FoodiesCollectionConfig): Cell {
  return beginCell()
    .storeUint(config.royaltyFactor, 16)
    .storeUint(config.royaltyBase, 16)
    .storeAddress(config.treasury)
    .endCell();
}

export function foodiesCollectionConfigToCell(config: FoodiesCollectionConfig): Cell {
  return beginCell()
    .storeAddress(config.owner)
    .storeUint(config.nextItemIndex ?? 0n, 64)
    .storeRef(contentCell(config.collectionContentUrl))
    .storeRef(royaltyCell(config))
    .endCell();
}

export class FoodiesCollection implements Contract {
  constructor(
    readonly address: Address,
    readonly init?: { code: Cell; data: Cell }
  ) {}

  static createFromConfig(config: FoodiesCollectionConfig, code: Cell, workchain = 0) {
    const data = foodiesCollectionConfigToCell(config);
    const init = { code, data };
    return new FoodiesCollection(contractAddress(workchain, init), init);
  }

  async sendDeploy(provider: ContractProvider, via: Sender, value = toNano('0.25')) {
    await provider.internal(via, {
      value,
      sendMode: SendMode.PAY_GAS_SEPARATELY,
      body: beginCell().endCell()
    });
  }
}
