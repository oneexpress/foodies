import 'dotenv/config';
import { Address, Cell } from '@ton/core';
import { NetworkProvider } from '@ton/blueprint';
import { FoodiesCollection } from '../wrappers/FoodiesCollection';
import * as fs from 'fs';

function must(k: string): string {
  const v = process.env[k];
  if (!v) throw new Error('Missing env: ' + k);
  return v;
}

function loadCode(): Cell {
  const files = fs.existsSync('build') ? fs.readdirSync('build') : [];
  const compiled = files.find(f => /FoodiesCollection.*\.compiled\.json$/i.test(f))
    || files.find(f => /collection.*\.compiled\.json$/i.test(f))
    || files.find(f => /\.compiled\.json$/i.test(f));

  if (!compiled) {
    throw new Error('No compiled JSON found in build/. Run npx blueprint build first.');
  }

  const j = JSON.parse(fs.readFileSync('build/' + compiled, 'utf8'));

  if (j.hex) return Cell.fromBoc(Buffer.from(j.hex, 'hex'))[0];
  if (j.boc) return Cell.fromBoc(Buffer.from(j.boc, 'base64'))[0];

  throw new Error('Unsupported compiled JSON format: ' + compiled);
}

export async function run(provider: NetworkProvider) {
  const owner = Address.parse(must('FOODIES_OWNER'));
  const treasury = Address.parse(must('FOODIES_TREASURY'));
  const url = must('FOODIES_COLLECTION_URL');
  const royalty = Number(process.env.FOODIES_ROYALTY_BP || '2500');

  const code = loadCode();

  const collection = provider.open(FoodiesCollection.createFromConfig({
    owner,
    treasury,
    collectionContentUrl: url,
    royaltyFactor: royalty,
    royaltyBase: 10000,
    nextItemIndex: 0n
  }, code));

  console.log('===== @foodies DEPLOY CONFIRM =====');
  console.log('Address:', collection.address.toString());
  console.log('Metadata:', url);
  console.log('Owner:', owner.toString());
  console.log('Treasury:', treasury.toString());
  console.log('Royalty:', royalty + '/10000');
  console.log('Deploy value: 0.25 TON');

  await collection.sendDeploy(provider.sender());
  await provider.waitForDeploy(collection.address);

  console.log('DEPLOYED_COLLECTION_ADDRESS=' + collection.address.toString());
}
