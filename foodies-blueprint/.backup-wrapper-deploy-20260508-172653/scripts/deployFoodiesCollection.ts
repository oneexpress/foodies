import 'dotenv/config';
import { Address, Cell } from '@ton/core';
import { NetworkProvider } from '@ton/blueprint';
import { FoodiesCollection } from '../wrappers/FoodiesCollection';
import * as fs from 'fs';

function must(name: string): string {
  const v = process.env[name];
  if (!v) throw new Error(`Missing env ${name}`);
  return v;
}

function loadCode(): Cell {
  const candidates = [
    'build/FoodiesCollection.compiled.json',
    'build/foodies_collection.compiled.json'
  ];

  for (const file of candidates) {
    if (fs.existsSync(file)) {
      const j = JSON.parse(fs.readFileSync(file, 'utf8'));
      return Cell.fromBase64(j.hex ? Buffer.from(j.hex, 'hex').toString('base64') : j.boc);
    }
  }

  throw new Error('Missing compiled collection JSON. Run: npx blueprint build');
}

export async function run(provider: NetworkProvider) {
  const owner = Address.parse(must('FOODIES_OWNER'));
  const treasury = Address.parse(must('FOODIES_TREASURY'));
  const url = must('FOODIES_COLLECTION_URL');

  const collection = provider.open(FoodiesCollection.createFromConfig({
    owner,
    treasury,
    collectionContentUrl: url,
    royaltyFactor: Number(process.env.FOODIES_ROYALTY_BP || 2500),
    royaltyBase: 10000,
    nextItemIndex: 0n
  }, loadCode()));

  console.log('===== @foodies COLLECTION DEPLOY =====');
  console.log('Address:', collection.address.toString());
  console.log('Metadata:', url);
  console.log('Owner:', owner.toString());
  console.log('Treasury:', treasury.toString());
  console.log('Royalty:', process.env.FOODIES_ROYALTY_BP || '2500', '/ 10000');

  await collection.sendDeploy(provider.sender());
  await provider.waitForDeploy(collection.address);

  console.log('DEPLOYED:', collection.address.toString());
}
