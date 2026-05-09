const FOODIES_CDN_BASE = 'https://cdn.jsdelivr.net/gh/oneexpress/foodies@main/public/metadata/foodies';
const { beginCell, Address, toNano } = require('@ton/core');

function arg(name, fallback='') {
  const p = process.argv.find(x => x.startsWith(`--${name}=`));
  return p ? p.split('=').slice(1).join('=') : fallback;
}

const uid = arg('uid', 'FOODIES-MINT-0001').replace(/[^A-Za-z0-9._-]/g, '-');
const star = parseInt(arg('star', '5'), 10);
const index = BigInt(arg('index', '0'));
const collection = arg('collection');
const owner = arg('owner');
const cdn = arg('cdn');

const metaUrl = `${cdn}/metadata/foodies/items/${uid}.json`;

const offchain = beginCell()
  .storeUint(0x01, 8)
  .storeStringTail(metaUrl)
  .endCell();

/*
  Standard NFT collection deploy/mint body:
  op = 1
  query_id
  item_index
  amount for NFT item deploy
  ref: nft item content/message body = owner_address + individual/offchain content ref
*/
const nftContent = beginCell()
  .storeAddress(Address.parse(owner))
  .storeRef(offchain)
  .endCell();

const body = beginCell()
  .storeUint(1, 32)
  .storeUint(Date.now(), 64)
  .storeUint(index, 64)
  .storeCoins(toNano('0.08'))
  .storeRef(nftContent)
  .endCell();

const boc = body.toBoc({ idx: false }).toString('base64');

console.log(JSON.stringify({
  ok: true,
  uid,
  star,
  collection,
  owner,
  index: index.toString(),
  metadata_url: metaUrl,
  payload: boc,
  payload_magic: Buffer.from(boc, 'base64').subarray(0,4).toString('hex')
}, null, 2));
