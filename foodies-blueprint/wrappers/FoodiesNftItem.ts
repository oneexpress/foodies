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

export type FoodiesNftItemConfig = {
  index: bigint;
  collectionAddress: Address;
};

export type V10NftData = {
  isInitialized: boolean;
  index: bigint;
  collectionAddress: Address;
  ownerAddress: Address | null;
  individualContent: Cell | null;
};

export function foodiesNftItemConfigToCell(config: FoodiesNftItemConfig): Cell {
  return beginCell()
    .storeUint(config.index, 64)
    .storeAddress(config.collectionAddress)
    .endCell();
}

export class FoodiesNftItem implements Contract {
  constructor(readonly address: Address, readonly init?: { code: Cell; data: Cell }) {}

  static createFromAddress(address: Address) {
    return new FoodiesNftItem(address);
  }

  static createFromConfig(config: FoodiesNftItemConfig, code: Cell, workchain = 0) {
    const data = foodiesNftItemConfigToCell(config);
    const init = { code, data };
    return new FoodiesNftItem(contractAddress(workchain, init), init);
  }

  async getNftData(provider: ContractProvider): Promise<V10NftData> {
    const res = await provider.get('get_nft_data', []);
    const initFlag = res.stack.readBigNumber();
    const index = res.stack.readBigNumber();
    const collectionAddress = res.stack.readAddress();
    const ownerAddress = res.stack.readAddressOpt();
    const individualContent = res.stack.readCellOpt();

    return {
      isInitialized: initFlag !== 0n,
      index,
      collectionAddress,
      ownerAddress,
      individualContent
    };
  }

  async sendTransfer(
    provider: ContractProvider,
    via: Sender,
    params: {
      value: bigint;
      queryId?: bigint;
      newOwner: Address;
      responseDestination?: Address | null;
      forwardAmount?: bigint;
      forwardPayload?: Cell | null;
    }
  ) {
    const body = beginCell()
      .storeUint(0x5fcc3d14, 32)
      .storeUint(params.queryId ?? 0n, 64)
      .storeAddress(params.newOwner)
      .storeAddress(params.responseDestination ?? null)
      .storeBit(0)
      .storeCoins(params.forwardAmount ?? 0n);

    if (params.forwardPayload) {
      body.storeBit(1).storeRef(params.forwardPayload);
    } else {
      body.storeBit(0);
    }

    await provider.internal(via, {
      value: params.value,
      sendMode: SendMode.PAY_GAS_SEPARATELY,
      body: body.endCell()
    });
  }
}
