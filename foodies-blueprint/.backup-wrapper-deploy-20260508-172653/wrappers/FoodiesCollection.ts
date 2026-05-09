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
  royaltyBase: number;
  royaltyFactor: number;
  nextItemIndex?: bigint;
};

export function contentCell(url: string): Cell {
  return beginCell()
    .storeUint(1, 8)
    .storeStringTail(url)
    .endCell();
}

export function royaltyParamsCell(config: FoodiesCollectionConfig): Cell {
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
    .storeRef(royaltyParamsCell(config))
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

  async getCollectionData(provider: ContractProvider) {
    const res = await provider.get('get_collection_data', []);
    return {
      nextItemIndex: res.stack.readBigNumber(),
      collectionContent: res.stack.readCell(),
      owner: res.stack.readAddress()
    };
  }

  async getRoyaltyParams(provider: ContractProvider) {
    const res = await provider.get('royalty_params', []);
    return {
      royaltyFactor: res.stack.readNumber(),
      royaltyBase: res.stack.readNumber(),
      royaltyAddress: res.stack.readAddress()
    };
  }
}
