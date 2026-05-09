import { Address, beginCell, toNano } from '@ton/core';

function arg(name: string, fallback = ''): string {
  const p = process.argv.find(v => v.startsWith('--' + name + '='));
  return p ? p.split('=').slice(1).join('=') : fallback;
}

function offchainContent(url: string) {
  return beginCell().storeUint(1, 8).storeStringTail(url).endCell();
}

try {
  const uid = arg('uid');
  const to = arg('to');
  const star = Number(arg('star', '1'));
  const collection = process.env.FOODIES_COLLECTION_ADDRESS || 'EQBP2ofSKqVWs_LlSpcpXKUOuqbHP6wpp2MljbbS8-dI-sbk';
  const amountTon = arg('amount', process.env.FOODIES_PUBLIC_MINT_ATTACH_TON || '0.50');
  const metaBase = process.env.FOODIES_ITEM_METADATA_BASE || 'https://expressvisa.one/metadata/foodies/items';
  const verifyBase = process.env.FOODIES_VERIFY_BASE || 'https://expressvisa.one/foodies-nft/verify.php?uid=';

  if (!uid) throw new Error('uid_required');
  if (!to) throw new Error('recipient_required');
  if (![1,3,5].includes(star)) throw new Error('invalid_star');

  const itemMetadataUrl = `${metaBase.replace(/\/+$/, '')}/${encodeURIComponent(uid)}.json`;
  const verifyUrl = `${verifyBase}${encodeURIComponent(uid)}`;
  const PMIN = 0x504d494e;
  const queryId = BigInt(Date.now());

  const payload = beginCell()
    .storeUint(PMIN, 32)
    .storeUint(queryId, 64)
    .storeAddress(Address.parse(to))
    .storeRef(offchainContent(itemMetadataUrl))
    .endCell();

  const payloadB64 = payload.toBoc().toString('base64');

  console.log(JSON.stringify({
    ok: true,
    state: 'prepared',
    action: 'public_mint',
    collection,
    to: Address.parse(to).toString(),
    uid,
    star,
    item_metadata_url: itemMetadataUrl,
    verify_url: verifyUrl,
    amount_ton: amountTon,
    amount_nano: toNano(amountTon).toString(),
    payload_boc_base64: payloadB64,
    payload_boc_hex: payload.toBoc().toString('hex'),
    tonconnect_message: {
      address: collection,
      amount: toNano(amountTon).toString(),
      payload: payloadB64
    },
    tonconnect: {
      validUntil: Math.floor(Date.now() / 1000) + 900,
      messages: [{
        address: collection,
        amount: toNano(amountTon).toString(),
        payload: payloadB64
      }]
    }
  }, null, 2));
} catch(e:any) {
  console.log(JSON.stringify({ok:false,error:String(e?.message || e)}, null, 2));
  process.exit(1);
}
