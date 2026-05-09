import * as path from 'path';
import * as dotenv from 'dotenv';
import { Address, beginCell, Cell, toNano } from '@ton/core';
import { compile, NetworkProvider } from '@ton/blueprint';
import { FoodiesNftCollection } from '../wrappers/FoodiesNftCollection';

dotenv.config({ path: path.join('/var/www/secure/.env') });
dotenv.config({ path: path.join(__dirname, '..', '.env') });

function required(name: string): string {
  const v = process.env[name];
  if (!v) throw new Error(`Missing env: ${name}`);
  return v;
}

function offchainCell(uri: string): Cell {
  return beginCell().storeUint(0x01, 8).storeBuffer(Buffer.from(uri, 'utf8')).endCell();
}

function contentCellFromEnv(): Cell {
  const collectionContent = offchainCell(required('FOODIES_COLLECTION_METADATA'));
  const commonContent = beginCell()
    .storeBuffer(Buffer.from(required('FOODIES_ITEM_METADATA_BASE') + '/', 'utf8'))
    .endCell();

  return beginCell()
    .storeRef(collectionContent)
    .storeRef(commonContent)
    .endCell();
}

function cfgRefFromEnv(): Cell {
  return beginCell()
    .storeUint(BigInt(required('FOODIES_ROYALTY_FACTOR')), 16)
    .storeUint(BigInt(required('FOODIES_ROYALTY_BASE')), 16)
    .storeAddress(Address.parse(required('FOODIES_TREASURY')))
    .storeAddress(Address.parse(required('FOODIES_TREASURY')))
    .storeUint(BigInt(required('FOODIES_DEFAULT_PAUSED')), 1)
    .storeCoins(toNano(required('FOODIES_MIN_STORAGE_RESERVE_TON')))
    .storeCoins(toNano(required('FOODIES_ITEM_DEPLOY_VALUE_TON')))
    .storeCoins(toNano(required('FOODIES_PUBLIC_MINT_ATTACH_TON')))
    .storeCoins(toNano(required('FOODIES_PRIMARY_TREASURY_TON')))
    .endCell();
}

export async function run(provider: NetworkProvider) {
  const owner = Address.parse(required('FOODIES_COLLECTION_OWNER'));
  const collectionCode = await compile('FoodiesNftCollection');
  const itemCode = await compile('FoodiesNftItem');

  const contract = provider.open(
    FoodiesNftCollection.createFromConfig(
      {
        owner,
        nextIndex: 0n,
        collectionContent: contentCellFromEnv(),
        itemCode,
        cfgRef: cfgRefFromEnv()
      },
      collectionCode
    )
  );

  console.log('========================================');
  console.log('DEPLOY FOODIES V10 GETGEMS COLLECTION');
  console.log('========================================');
  console.log('Owner      :', owner.toString());
  console.log('Treasury   :', required('FOODIES_TREASURY'));
  console.log('Collection :', contract.address.toString());
  console.log('Metadata   :', required('FOODIES_COLLECTION_METADATA'));
  console.log('Item Base  :', required('FOODIES_ITEM_METADATA_BASE'));
  console.log('Royalty    :', required('FOODIES_ROYALTY_FACTOR') + '/' + required('FOODIES_ROYALTY_BASE'));
  console.log('========================================');

  await contract.sendDeploy(provider.sender(), toNano(required('FOODIES_DEPLOY_VALUE_TON')));
  await provider.waitForDeploy(contract.address);

  console.log('DEPLOYED_FOODIES_COLLECTION_ADDRESS=' + contract.address.toString());
}
